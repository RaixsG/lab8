-- Laboratorio 08 - Ejercicio 1: clientes
-- Script completo para crear la base de datos y la tabla desde cero.
--
-- Uso manual:
--   mysql -u root -p < sql/tcliente.sql
--   o pegar todo el contenido en phpMyAdmin / DBeaver / HeidiSQL.
--

SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS bdalumno
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE bdalumno;

CREATE TABLE IF NOT EXISTS tcliente (
  id INT AUTO_INCREMENT PRIMARY KEY,
  dni CHAR(8) NOT NULL,
  apellidos VARCHAR(50) NOT NULL,
  nombres VARCHAR(50) NOT NULL,
  email VARCHAR(50) NOT NULL,
  celular CHAR(9) NOT NULL,
  direccion VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

