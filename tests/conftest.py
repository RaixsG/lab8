import base64
import os
from pathlib import Path

import pytest
from selenium import webdriver
from selenium.webdriver.chrome.options import Options


REPORTS_DIR = Path(__file__).parent / "reports"
SCREENSHOTS_DIR = REPORTS_DIR / "screenshots"


def pytest_addoption(parser: pytest.Parser) -> None:
    parser.addoption(
        "--base-url",
        action="store",
        default=os.environ.get("BASE_URL", "http://localhost:8080"),
        help="Base URL of the running PHP application.",
    )
    parser.addoption(
        "--headed",
        action="store_true",
        default=False,
        help="Run Chrome in visible (non-headless) mode.",
    )


@pytest.fixture(scope="session")
def base_url(request: pytest.FixtureRequest) -> str:
    return str(request.config.getoption("--base-url")).rstrip("/")


@pytest.fixture
def driver(request: pytest.FixtureRequest):
    options = Options()
    if not request.config.getoption("--headed"):
        options.add_argument("--headless=new")
    options.add_argument("--window-size=1280,900")
    options.add_argument("--no-sandbox")
    options.add_argument("--disable-dev-shm-usage")

    web_driver = webdriver.Chrome(options=options)
    web_driver.implicitly_wait(2)
    yield web_driver
    web_driver.quit()


@pytest.hookimpl(hookwrapper=True)
def pytest_runtest_makereport(item: pytest.Item, call: pytest.CallInfo):
    outcome = yield
    report = outcome.get_result()

    if report.when != "call":
        return

    web_driver = item.funcargs.get("driver")
    if web_driver is None:
        return

    SCREENSHOTS_DIR.mkdir(parents=True, exist_ok=True)
    status_label = "FAIL" if report.failed else "PASS"
    screenshot_path = SCREENSHOTS_DIR / f"{item.name}_{status_label}.png"
    try:
        web_driver.save_screenshot(str(screenshot_path))
    except Exception:
        return

    pytest_html = item.config.pluginmanager.getplugin("html")
    if pytest_html is not None and screenshot_path.exists():
        encoded = base64.b64encode(screenshot_path.read_bytes()).decode("ascii")
        extras = getattr(report, "extras", [])
        extras.append(pytest_html.extras.png(encoded))
        report.extras = extras
