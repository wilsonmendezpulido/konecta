<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## CRUD PRODUCTO - ACCESO POR API
--------------------------------
SHOW
http://localhost/konecta/public/api/producto/5

CREATE
http://localhost/konecta/public/api/producto

{
    "nombre": "Coca-Cola 1350 Ml",
    "referencia": "C001",
    "precio": "3500",
    "peso": "1",
    "categoria": "Bebidas",
    "stock": "10"
}

UPDATE
http://localhost/konecta/public/api/producto

{
    "id": 1,
    "nombre": "Coca-Cola 350 Ml",
    "referencia": "C001",
    "precio": "1500",
    "peso": "1",
    "categoria": "Bebidas",
    "stock": "10"
}

DELETE
http://localhost/konecta/public/api/producto/3

VENTA
------------

http://localhost/konecta/public/api/ventaproducto

{
    "id_producto": 1,
    "cantidad": 1,
    "detalle": "venta de prueba"
}


CONFIGURACIONES .ENV

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=konecta
DB_USERNAME=konecta
DB_PASSWORD=konecta

TABLAS

CREATE TABLE `productos` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`nombre` VARCHAR(250) NOT NULL COLLATE 'utf8mb4_general_ci',
	`referencia` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_general_ci',
	`precio` INT(20) NOT NULL,
	`peso` INT(20) NOT NULL,
	`categoria` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_general_ci',
	`stock` INT(10) NOT NULL,
	`fecha_creacion` TIMESTAMP NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
	PRIMARY KEY (`id`) USING BTREE
)
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;

CREATE TABLE `ventas` (
	`id_producto` INT(10) UNSIGNED NOT NULL,
	`cantidad` INT(10) NOT NULL,
	`detalle` VARCHAR(250) NOT NULL DEFAULT '' COLLATE 'utf8mb4_general_ci',
	`created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
	`updated_at` TIMESTAMP NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
	INDEX `FK_ventas_productos` (`id_producto`) USING BTREE,
	CONSTRAINT `FK_ventas_productos` FOREIGN KEY (`id_producto`) REFERENCES `konecta`.`productos` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION
)
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
;



CONSULTAS
------------

Realizar una consulta que permita conocer cuál es el producto que más stock tiene.
-----------------------------------------------------------------------------------
SELECT id, nombre, referencia, precio, peso, categoria, MAX(stock) stock, fecha_creacion
FROM productos;


Realizar una consulta que permita conocer cuál es el producto más vendido.
-----------------------------------------------------------------------------------
SELECT V.id_producto, P.nombre, SUM(cantidad) Cantidad FROM ventas V
JOIN productos P ON V.id_producto = P.Id
GROUP BY id_producto
ORDER BY cantidad DESC
LIMIT 1;

SELECT id_producto, nombre, MAX(cantidad) cantidad FROM  (
SELECT V.id_producto, P.nombre, SUM(cantidad) cantidad FROM ventas V
JOIN productos P ON V.id_producto = P.Id
GROUP BY id_producto) producto;
