<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class InitCargaConsolidada extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    //     $tableCabeceraQuery="
    //     CREATE TABLE carga_consolidada_cotizaciones_cabecera (
    //         ID_Cotizacion INT NOT NULL AUTO_INCREMENT,
    //         Fe_Creacion DATE NOT NULL,
    //         N_Cliente TEXT(65535) NULL,
    //         Empresa TEXT(65535) NULL,
    //         Cotizacion DECIMAL(10,2) NULL,
    //         ID_Tipo_Cliente INT NOT NULL,
    //         Cotizacion_Status enum('Pendiente','Cotizado') NULL default 'Pendiente',
    //         PRIMARY KEY (ID_Cotizacion)
    //     )";
    //     $tableTipoTributoTable="CREATE TABLE tipo_carga_consolidada_cotizaciones_tributo (
    //         ID_Tipo_Tributo INT NOT NULL AUTO_INCREMENT,
    //         Nombre VARCHAR(255) NULL,
    //         table_key VARCHAR(50) NULL,
    //         PRIMARY KEY (ID_Tipo_Tributo)
    //     );" ;
    //     $insertIntoTipoTributo="INSERT INTO tipo_carga_consolidada_cotizaciones_tributo
    //     ( Nombre, table_key)
    //     VALUES( 'Ad Valorem', 'ad-valorem'),
    //     ('IGV', 'igv'),
    //     ('IPM', 'ipm'),
    //     ('Percepcion', 'percepcion'),
    //     ('Valoracion', 'valoracion'),
    //     ('AntiDumping', 'antidumping');

    //    ";
    //    $tableDetallesProveedor="CREATE TABLE carga_consolidada_cotizaciones_detalles_proovedor (
    //     ID_Proveedor INT NOT NULL AUTO_INCREMENT,
    //     ID_Cotizacion INT NOT NULL,
    //     CBM_Total DECIMAL(10,2) NULL,
    //     Peso_Total DECIMAL(10,2) NULL,
    //     URL_Proforma TEXT(65535) NULL,
    //     URL_Packing TEXT(65535) NULL,
    //     PRIMARY KEY (ID_Proveedor),
    //     FOREIGN KEY (ID_Cotizacion) REFERENCES carga_consolidada_cotizaciones_cabecera(ID_Cotizacion)
    //     );";
    //     $tableDetallesProductos="
    //     CREATE TABLE carga_consolidada_cotizaciones_detalles_producto (
    //         ID_Producto INT NOT NULL AUTO_INCREMENT,
    //         ID_Cotizacion INT NOT NULL,
    //         ID_Proveedor INT NOT NULL,
    //         URL_Image TEXT(65535) NULL,
    //         URL_Link TEXT(65535) NULL,
    //         Nombre_Comercial VARCHAR(500) NULL,
    //         Uso TEXT(65535) NULL,
    //         Cantidad DECIMAL(10,2) NULL,
    //         Valor_unitario DECIMAL(10,2) NULL,
    //         PRIMARY KEY (ID_Producto),
    //         FOREIGN KEY (ID_Cotizacion) REFERENCES carga_consolidada_cotizaciones_cabecera(ID_Cotizacion),
    //         FOREIGN KEY (ID_Proveedor) REFERENCES carga_consolidada_cotizaciones_detalles_proovedor(ID_Proveedor)
    //     );";  
    //     $tableDetallesTributo="CREATE TABLE carga_consolidada_cotizaciones_detalles_tributo (
    //         ID_Tributo INT NOT NULL AUTO_INCREMENT,
    //         ID_Tipo_Tributo INT NOT NULL,
    //         ID_Producto INT NOT NULL,
    //         ID_Proveedor INT NOT NULL,
    //         ID_Cotizacion INT NOT NULL,
    //         Status enum('Pending','Completed') NULL default 'Pending',
    //         value DECIMAL(10,2) NULL,
    //         PRIMARY KEY (ID_Tributo),
    //         FOREIGN KEY (ID_Tipo_Tributo) REFERENCES tipo_carga_consolidada_cotizaciones_tributo(ID_Tipo_Tributo),
    //         FOREIGN KEY (ID_Producto) REFERENCES carga_consolidada_cotizaciones_detalles_producto(ID_Producto),
    //         FOREIGN KEY (ID_Proveedor) REFERENCES carga_consolidada_cotizaciones_detalles_proovedor(ID_Proveedor),
    //         FOREIGN KEY (ID_Cotizacion) REFERENCES carga_consolidada_cotizaciones_cabecera(ID_Cotizacion)
    //     );";
    //     DB::statement($tableCabeceraQuery);
    //     DB::statement($tableTipoTributoTable);
    //     DB::statement($insertIntoTipoTributo);
    //     DB::statement($tableDetallesProveedor);
    //     DB::statement($tableDetallesProductos);
    //     DB::statement($tableDetallesTributo);
        $query="-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
        --
        -- Host: localhost    Database: probussiness
        -- ------------------------------------------------------
        -- Server version	5.5.5-10.4.11-MariaDB
        
        /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
        /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
        /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
        /*!50503 SET NAMES utf8mb4 */;
        /*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
        /*!40103 SET TIME_ZONE='+00:00' */;
        /*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
        /*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
        /*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
        /*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
        
        --
        -- Table structure for table `carga_consolidada_cbm_tarifas`
        --
        
        DROP TABLE IF EXISTS `carga_consolidada_cbm_tarifas`;
        /*!40101 SET @saved_cs_client     = @@character_set_client */;
        /*!50503 SET character_set_client = utf8mb4 */;
        CREATE TABLE `carga_consolidada_cbm_tarifas` (
          `id_tarifa` int(11) NOT NULL AUTO_INCREMENT,
          `id_tipo_tarifa` int(11) NOT NULL,
          `id_tipo_cliente` int(11) NOT NULL,
          `limite_inf` decimal(10,2) NOT NULL,
          `limite_sup` decimal(10,2) NOT NULL,
          `currency` varchar(50) NOT NULL DEFAULT 'USD',
          `tarifa` decimal(10,2) NOT NULL,
          `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
          `updated_at` datetime DEFAULT NULL,
          PRIMARY KEY (`id_tarifa`)
        ) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4;
        /*!40101 SET character_set_client = @saved_cs_client */;
        
        --
        -- Dumping data for table `carga_consolidada_cbm_tarifas`
        --
        
        LOCK TABLES `carga_consolidada_cbm_tarifas` WRITE;
        /*!40000 ALTER TABLE `carga_consolidada_cbm_tarifas` DISABLE KEYS */;
        INSERT INTO `carga_consolidada_cbm_tarifas` VALUES (1,1,1,0.10,0.50,'USD',250.00,'2024-05-17 22:33:10',NULL),(2,1,1,0.60,1.00,'USD',350.00,'2024-05-17 22:33:10',NULL),(3,2,1,1.10,2.00,'USD',350.00,'2024-05-17 22:33:10',NULL),(4,2,1,2.10,3.00,'USD',325.00,'2024-05-17 22:33:10',NULL),(5,2,1,3.10,4.00,'USD',300.00,'2024-05-17 22:33:10',NULL),(6,2,1,4.10,999999.00,'USD',280.00,'2024-05-17 22:33:10',NULL),(7,1,2,0.10,0.50,'USD',250.00,'2024-05-17 22:33:10',NULL),(8,1,2,0.60,1.00,'USD',325.00,'2024-05-17 22:33:10',NULL),(9,2,2,1.10,2.00,'USD',325.00,'2024-05-17 22:33:10',NULL),(10,2,2,2.10,3.00,'USD',300.00,'2024-05-17 22:33:10',NULL),(11,2,2,3.10,4.00,'USD',275.00,'2024-05-17 22:33:10',NULL),(12,2,2,4.10,999999.00,'USD',250.00,'2024-05-17 22:33:10',NULL);
        /*!40000 ALTER TABLE `carga_consolidada_cbm_tarifas` ENABLE KEYS */;
        UNLOCK TABLES;
        
        --
        -- Table structure for table `tipo_carga_consolidada_cotizaciones_tributo`
        --
        
        DROP TABLE IF EXISTS `tipo_carga_consolidada_cotizaciones_tributo`;
        /*!40101 SET @saved_cs_client     = @@character_set_client */;
        /*!50503 SET character_set_client = utf8mb4 */;
        CREATE TABLE `tipo_carga_consolidada_cotizaciones_tributo` (
          `ID_Tipo_Tributo` int(11) NOT NULL AUTO_INCREMENT,
          `Nombre` varchar(255) DEFAULT NULL,
          `table_key` varchar(50) DEFAULT NULL,
          PRIMARY KEY (`ID_Tipo_Tributo`)
        ) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;
        /*!40101 SET character_set_client = @saved_cs_client */;
        
        --
        -- Dumping data for table `tipo_carga_consolidada_cotizaciones_tributo`
        --
        
        LOCK TABLES `tipo_carga_consolidada_cotizaciones_tributo` WRITE;
        /*!40000 ALTER TABLE `tipo_carga_consolidada_cotizaciones_tributo` DISABLE KEYS */;
        INSERT INTO `tipo_carga_consolidada_cotizaciones_tributo` VALUES (1,'Ad Valorem','ad-valorem'),(2,'IGV','igv'),(3,'IPM','ipm'),(4,'Percepcion','percepcion'),(5,'Valoracion','valoracion'),(6,'AntiDumping','antidumping');
        /*!40000 ALTER TABLE `tipo_carga_consolidada_cotizaciones_tributo` ENABLE KEYS */;
        UNLOCK TABLES;
        
        --
        -- Table structure for table `carga_consolidada_cotizaciones_detalles_tributo`
        --
        
        DROP TABLE IF EXISTS `carga_consolidada_cotizaciones_detalles_tributo`;
        /*!40101 SET @saved_cs_client     = @@character_set_client */;
        /*!50503 SET character_set_client = utf8mb4 */;
        CREATE TABLE `carga_consolidada_cotizaciones_detalles_tributo` (
          `ID_Tributo` int(11) NOT NULL AUTO_INCREMENT,
          `ID_Tipo_Tributo` int(11) NOT NULL,
          `ID_Producto` int(11) NOT NULL,
          `ID_Proveedor` int(11) NOT NULL,
          `ID_Cotizacion` int(11) NOT NULL,
          `Status` enum('Pending','Completed') DEFAULT 'Pending',
          `value` decimal(10,2) DEFAULT NULL,
          PRIMARY KEY (`ID_Tributo`),
          KEY `ID_Tipo_Tributo` (`ID_Tipo_Tributo`),
          KEY `ID_Producto` (`ID_Producto`),
          KEY `ID_Proveedor` (`ID_Proveedor`),
          KEY `ID_Cotizacion` (`ID_Cotizacion`),
          CONSTRAINT `carga_consolidada_cotizaciones_detalles_tributo_ibfk_1` FOREIGN KEY (`ID_Tipo_Tributo`) REFERENCES `tipo_carga_consolidada_cotizaciones_tributo` (`ID_Tipo_Tributo`),
          CONSTRAINT `carga_consolidada_cotizaciones_detalles_tributo_ibfk_2` FOREIGN KEY (`ID_Producto`) REFERENCES `carga_consolidada_cotizaciones_detalles_producto` (`ID_Producto`),
          CONSTRAINT `carga_consolidada_cotizaciones_detalles_tributo_ibfk_3` FOREIGN KEY (`ID_Proveedor`) REFERENCES `carga_consolidada_cotizaciones_detalles_proovedor` (`ID_Proveedor`),
          CONSTRAINT `carga_consolidada_cotizaciones_detalles_tributo_ibfk_4` FOREIGN KEY (`ID_Cotizacion`) REFERENCES `carga_consolidada_cotizaciones_cabecera` (`ID_Cotizacion`)
        ) ENGINE=InnoDB AUTO_INCREMENT=145 DEFAULT CHARSET=utf8mb4;
        /*!40101 SET character_set_client = @saved_cs_client */;
        
        --
        -- Dumping data for table `carga_consolidada_cotizaciones_detalles_tributo`
        --
        
        LOCK TABLES `carga_consolidada_cotizaciones_detalles_tributo` WRITE;
        /*!40000 ALTER TABLE `carga_consolidada_cotizaciones_detalles_tributo` DISABLE KEYS */;
        INSERT INTO `carga_consolidada_cotizaciones_detalles_tributo` VALUES (1,1,1,1,1,'Completed',40.00),(2,2,1,1,1,'Completed',16.00),(3,3,1,1,1,'Completed',2.00),(4,4,1,1,1,'Completed',3.50),(5,5,1,1,1,'Completed',120.00),(6,6,1,1,1,'Completed',1.00),(7,1,2,2,2,'Completed',2.00),(8,2,2,2,2,'Completed',16.00),(9,3,2,2,2,'Completed',2.00),(10,4,2,2,2,'Completed',3.50),(11,5,2,2,2,'Completed',2.00),(12,6,2,2,2,'Completed',1.00),(13,1,3,3,3,'Pending',NULL),(14,2,3,3,3,'Pending',NULL),(15,3,3,3,3,'Pending',NULL),(16,4,3,3,3,'Pending',NULL),(17,5,3,3,3,'Pending',NULL),(18,6,3,3,3,'Pending',NULL),(19,1,4,4,3,'Pending',0.00),(20,2,4,4,3,'Pending',16.00),(21,3,4,4,3,'Pending',2.00),(22,4,4,4,3,'Pending',3.50),(23,5,4,4,3,'Pending',0.00),(24,6,4,4,3,'Pending',0.00),(25,1,5,1,1,'Completed',0.00),(26,2,5,1,1,'Completed',16.00),(27,3,5,1,1,'Completed',2.00),(28,4,5,1,1,'Completed',3.50),(29,5,5,1,1,'Completed',0.00),(30,6,5,1,1,'Completed',1000.00),(31,1,6,1,1,'Pending',0.00),(32,2,6,1,1,'Pending',16.00),(33,3,6,1,1,'Pending',2.00),(34,4,6,1,1,'Pending',3.50),(35,5,6,1,1,'Pending',0.00),(36,6,6,1,1,'Pending',0.00),(37,1,8,5,1,'Completed',0.00),(38,2,8,5,1,'Completed',16.00),(39,3,8,5,1,'Completed',2.00),(40,4,8,5,1,'Completed',3.50),(41,5,8,5,1,'Completed',0.00),(42,6,8,5,1,'Completed',0.00),(43,1,9,6,2,'Completed',0.00),(44,2,9,6,2,'Completed',16.00),(45,3,9,6,2,'Completed',2.00),(46,4,9,6,2,'Completed',3.50),(47,5,9,6,2,'Completed',0.00),(48,6,9,6,2,'Completed',0.00),(55,1,12,8,5,'Completed',0.00),(56,2,12,8,5,'Completed',16.00),(57,3,12,8,5,'Completed',2.00),(58,4,12,8,5,'Completed',3.50),(59,5,12,8,5,'Completed',0.00),(60,6,12,8,5,'Completed',120.00),(61,1,13,9,17,'Completed',0.00),(62,2,13,9,17,'Completed',16.00),(63,3,13,9,17,'Completed',2.00),(64,4,13,9,17,'Completed',3.50),(65,5,13,9,17,'Completed',0.00),(66,6,13,9,17,'Completed',0.00),(67,1,14,10,21,'Pending',NULL),(68,2,14,10,21,'Pending',NULL),(69,3,14,10,21,'Pending',NULL),(70,4,14,10,21,'Pending',NULL),(71,5,14,10,21,'Pending',NULL),(72,6,14,10,21,'Pending',NULL),(73,1,15,11,22,'Pending',NULL),(74,2,15,11,22,'Pending',NULL),(75,3,15,11,22,'Pending',NULL),(76,4,15,11,22,'Pending',NULL),(77,5,15,11,22,'Pending',NULL),(78,6,15,11,22,'Pending',NULL),(79,1,16,12,23,'Pending',NULL),(80,2,16,12,23,'Pending',NULL),(81,3,16,12,23,'Pending',NULL),(82,4,16,12,23,'Pending',NULL),(83,5,16,12,23,'Pending',NULL),(84,6,16,12,23,'Pending',NULL),(85,1,17,13,24,'Pending',NULL),(86,2,17,13,24,'Pending',NULL),(87,3,17,13,24,'Pending',NULL),(88,4,17,13,24,'Pending',NULL),(89,5,17,13,24,'Pending',NULL),(90,6,17,13,24,'Pending',NULL),(91,1,18,14,25,'Pending',NULL),(92,2,18,14,25,'Pending',NULL),(93,3,18,14,25,'Pending',NULL),(94,4,18,14,25,'Pending',NULL),(95,5,18,14,25,'Pending',NULL),(96,6,18,14,25,'Pending',NULL),(97,1,19,15,26,'Pending',NULL),(98,2,19,15,26,'Pending',NULL),(99,3,19,15,26,'Pending',NULL),(100,4,19,15,26,'Pending',NULL),(101,5,19,15,26,'Pending',NULL),(102,6,19,15,26,'Pending',NULL),(103,1,20,16,27,'Pending',NULL),(104,2,20,16,27,'Pending',NULL),(105,3,20,16,27,'Pending',NULL),(106,4,20,16,27,'Pending',NULL),(107,5,20,16,27,'Pending',NULL),(108,6,20,16,27,'Pending',NULL),(109,1,21,16,27,'Pending',NULL),(110,2,21,16,27,'Pending',NULL),(111,3,21,16,27,'Pending',NULL),(112,4,21,16,27,'Pending',NULL),(113,5,21,16,27,'Pending',NULL),(114,6,21,16,27,'Pending',NULL),(115,1,22,17,28,'Pending',NULL),(116,2,22,17,28,'Pending',NULL),(117,3,22,17,28,'Pending',NULL),(118,4,22,17,28,'Pending',NULL),(119,5,22,17,28,'Pending',NULL),(120,6,22,17,28,'Pending',NULL),(121,1,23,18,14,'Pending',0.00),(122,2,23,18,14,'Pending',16.00),(123,3,23,18,14,'Pending',2.00),(124,4,23,18,14,'Pending',3.50),(125,5,23,18,14,'Pending',0.00),(126,6,23,18,14,'Pending',0.00),(127,1,24,19,13,'Completed',0.00),(128,2,24,19,13,'Completed',16.00),(129,3,24,19,13,'Completed',2.00),(130,4,24,19,13,'Completed',3.50),(131,5,24,19,13,'Completed',0.00),(132,6,24,19,13,'Completed',0.00),(133,1,25,20,18,'Pending',0.00),(134,2,25,20,18,'Pending',16.00),(135,3,25,20,18,'Pending',2.00),(136,4,25,20,18,'Pending',3.50),(137,5,25,20,18,'Pending',0.00),(138,6,25,20,18,'Pending',0.00),(139,1,26,9,17,'Pending',0.00),(140,2,26,9,17,'Pending',16.00),(141,3,26,9,17,'Pending',2.00),(142,4,26,9,17,'Pending',3.50),(143,5,26,9,17,'Pending',0.00),(144,6,26,9,17,'Pending',0.00);
        /*!40000 ALTER TABLE `carga_consolidada_cotizaciones_detalles_tributo` ENABLE KEYS */;
        UNLOCK TABLES;
        
        --
        -- Table structure for table `carga_consolidada_cotizaciones_detalles_producto`
        --
        
        DROP TABLE IF EXISTS `carga_consolidada_cotizaciones_detalles_producto`;
        /*!40101 SET @saved_cs_client     = @@character_set_client */;
        /*!50503 SET character_set_client = utf8mb4 */;
        CREATE TABLE `carga_consolidada_cotizaciones_detalles_producto` (
          `ID_Producto` int(11) NOT NULL AUTO_INCREMENT,
          `ID_Cotizacion` int(11) NOT NULL,
          `ID_Proveedor` int(11) NOT NULL,
          `URL_Image` mediumtext DEFAULT NULL,
          `URL_Link` mediumtext DEFAULT NULL,
          `Nombre_Comercial` varchar(500) DEFAULT NULL,
          `Uso` mediumtext DEFAULT NULL,
          `Cantidad` decimal(10,2) DEFAULT NULL,
          `Valor_unitario` decimal(10,2) DEFAULT NULL,
          PRIMARY KEY (`ID_Producto`),
          KEY `ID_Cotizacion` (`ID_Cotizacion`),
          KEY `ID_Proveedor` (`ID_Proveedor`),
          CONSTRAINT `carga_consolidada_cotizaciones_detalles_producto_ibfk_1` FOREIGN KEY (`ID_Cotizacion`) REFERENCES `carga_consolidada_cotizaciones_cabecera` (`ID_Cotizacion`),
          CONSTRAINT `carga_consolidada_cotizaciones_detalles_producto_ibfk_2` FOREIGN KEY (`ID_Proveedor`) REFERENCES `carga_consolidada_cotizaciones_detalles_proovedor` (`ID_Proveedor`)
        ) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4;
        /*!40101 SET character_set_client = @saved_cs_client */;
        
        --
        -- Dumping data for table `carga_consolidada_cotizaciones_detalles_producto`
        --
        
        LOCK TABLES `carga_consolidada_cotizaciones_detalles_producto` WRITE;
        /*!40000 ALTER TABLE `carga_consolidada_cotizaciones_detalles_producto` DISABLE KEYS */;
        INSERT INTO `carga_consolidada_cotizaciones_detalles_producto` VALUES (1,1,1,'C:\\Users\\Admin\\Desktop\\Proveedores\\probussiness-back\\storage\\app\\public\\imagenes\\imagen_6647d09189832.png\\m2myvdEA3mIgBuSYzj1reY1olQmVX98tY7X9RTlS.jpg','https://www.bata.pe/zapatos-de-vestir-bata-para-hombre-821493039/p?idsku=821493042&gad_source=1&gclid=Cj0KCQjwgJyyBhCGARIsAK8LVLNBwT4aiB1FKJxBVz2wIEoi6bkKF94yRdercPlxwJR7EY-RmyiAfHYaAlaWEALw_wcB','Zapatos','para los pies',10.00,100.00),(2,2,2,'storage/app/public/imagenes/imagen_6647de4b21a19.png/stkFF4il1tct4ZlnYWwpdhTnOvnlRMc7rjD9MOmT.png','123123','Zapatos','312312',101.00,10.00),(3,3,3,'storage/app/public/imagenes/imagen_6647de6e03336.png/ZNkpFPjHmDrzT5DxcHL4yhPOUqZWcZ7DyXlnJR4s.png','123123','12312','312312',100.00,10.00),(4,3,4,NULL,'','','',0.00,0.00),(5,1,1,NULL,'','Zapatillas','waasda',100.00,10.00),(6,1,1,NULL,'','Audifonos','para las orejas ',100.00,10.00),(8,1,5,NULL,'','Lentes','Para los ojos',80.00,5.00),(9,2,6,NULL,'121','Zapatillas','para los pies ',212.00,121.00),(12,5,8,'storage/app/public/imagenes/imagen_664bc5031d848.png/3YRK3dwGWvfyGcrJu8ofr0Tx1Hs1LlauDfSFI7MI.png','sadas','as','asdas',10.00,100.00),(13,17,9,'storage/app/1','','','',0.00,0.00),(14,21,10,'storage/app/1',NULL,NULL,NULL,NULL,NULL),(15,22,11,'storage/app/1','CXDS','12','1121',1212.00,0.00),(16,23,12,'storage/app/1','1212','1','1212',12.00,NULL),(17,24,13,'storage/app/1','1212','1','1212',12.00,NULL),(18,25,14,'storage/app/1','1212','1212','121',1212.00,NULL),(19,26,15,'storage/app/1','1212','1212','121',1212.00,NULL),(20,27,16,'storage/app/1','312312','2132','1321321',312.00,0.00),(21,27,16,'storage/app/1','312312','2132','1321321',312.00,0.00),(22,28,17,'storage/app/1','21312','23213','1312',312.00,NULL),(23,14,18,NULL,'','','',0.00,0.00),(24,13,19,NULL,'','','',0.00,0.00),(25,18,20,NULL,'','','',0.00,0.00),(26,17,9,NULL,'','','',0.00,0.00),(27,17,9,NULL,'','','',0.00,0.00);
        /*!40000 ALTER TABLE `carga_consolidada_cotizaciones_detalles_producto` ENABLE KEYS */;
        UNLOCK TABLES;
        
        --
        -- Table structure for table `carga_consolidada_cotizaciones_detalles_proovedor`
        --
        
        DROP TABLE IF EXISTS `carga_consolidada_cotizaciones_detalles_proovedor`;
        /*!40101 SET @saved_cs_client     = @@character_set_client */;
        /*!50503 SET character_set_client = utf8mb4 */;
        CREATE TABLE `carga_consolidada_cotizaciones_detalles_proovedor` (
          `ID_Proveedor` int(11) NOT NULL AUTO_INCREMENT,
          `ID_Cotizacion` int(11) NOT NULL,
          `CBM_Total` decimal(10,2) DEFAULT NULL,
          `Peso_Total` decimal(10,2) DEFAULT NULL,
          `URL_Proforma` mediumtext DEFAULT NULL,
          `URL_Packing` mediumtext DEFAULT NULL,
          PRIMARY KEY (`ID_Proveedor`),
          KEY `ID_Cotizacion` (`ID_Cotizacion`),
          CONSTRAINT `carga_consolidada_cotizaciones_detalles_proovedor_ibfk_1` FOREIGN KEY (`ID_Cotizacion`) REFERENCES `carga_consolidada_cotizaciones_cabecera` (`ID_Cotizacion`)
        ) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4;
        /*!40101 SET character_set_client = @saved_cs_client */;
        
        --
        -- Dumping data for table `carga_consolidada_cotizaciones_detalles_proovedor`
        --
        
        LOCK TABLES `carga_consolidada_cotizaciones_detalles_proovedor` WRITE;
        /*!40000 ALTER TABLE `carga_consolidada_cotizaciones_detalles_proovedor` DISABLE KEYS */;
        INSERT INTO `carga_consolidada_cotizaciones_detalles_proovedor` VALUES (1,1,10.00,123.00,'public/proformas/proforma_6647d091869e4.pdf/xcniKkPJqzudoU7NlB10UnZEYGwlS196X3eruHZ3.jpg','public/packings/packing_6647d09188ecf.pdf/g4BvxaxgBpJ1vCwBnITDGO9ukBn0vcEqFiXtMaee.jpg'),(2,2,1.00,123.00,NULL,NULL),(3,3,100.00,123.00,NULL,NULL),(4,3,10.00,50.00,NULL,NULL),(5,1,2.00,180.00,NULL,NULL),(6,2,2.00,221.00,NULL,NULL),(8,5,10.00,124.00,NULL,NULL),(9,17,12.00,1212.00,NULL,NULL),(10,21,121.00,21.00,NULL,NULL),(11,22,121.00,121.00,NULL,NULL),(12,23,1212.00,12.00,NULL,NULL),(13,24,1212.00,12.00,NULL,NULL),(14,25,1212.00,131.00,NULL,NULL),(15,26,1212.00,131.00,NULL,NULL),(16,27,3123.00,312.00,NULL,NULL),(17,28,1212.00,121.00,NULL,NULL),(18,14,0.00,0.00,NULL,NULL),(19,13,0.00,0.00,NULL,NULL),(20,18,0.00,0.00,NULL,NULL);
        /*!40000 ALTER TABLE `carga_consolidada_cotizaciones_detalles_proovedor` ENABLE KEYS */;
        UNLOCK TABLES;
        
        --
        -- Table structure for table `carga_consolidada_cotizaciones_detalle`
        --
        
        DROP TABLE IF EXISTS `carga_consolidada_cotizaciones_detalle`;
        /*!40101 SET @saved_cs_client     = @@character_set_client */;
        /*!50503 SET character_set_client = utf8mb4 */;
        CREATE TABLE `carga_consolidada_cotizaciones_detalle` (
          `ID_Detalle` int(11) NOT NULL AUTO_INCREMENT,
          `ID_Cotizacion` int(11) NOT NULL,
          `DNI` varchar(20) NOT NULL,
          `Nombres` varchar(255) NOT NULL,
          `Apellidos` varchar(255) NOT NULL,
          `Telefono` varchar(20) NOT NULL,
          `CBM_Total` decimal(10,2) NOT NULL,
          `Peso_Total` decimal(10,2) NOT NULL,
          PRIMARY KEY (`ID_Detalle`),
          KEY `ID_Cotizacion` (`ID_Cotizacion`),
          CONSTRAINT `carga_consolidada_cotizaciones_detalle_ibfk_1` FOREIGN KEY (`ID_Cotizacion`) REFERENCES `carga_consolidada_cotizaciones_cabecera` (`ID_Cotizacion`)
        ) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4;
        /*!40101 SET character_set_client = @saved_cs_client */;
        
        --
        -- Dumping data for table `carga_consolidada_cotizaciones_detalle`
        --
        
        LOCK TABLES `carga_consolidada_cotizaciones_detalle` WRITE;
        /*!40000 ALTER TABLE `carga_consolidada_cotizaciones_detalle` DISABLE KEYS */;
        INSERT INTO `carga_consolidada_cotizaciones_detalle` VALUES (1,1,'74645561','Francis','Torres','912705923',12.00,303.00),(2,2,'10430047','Maria','Cuya','49489121',3.00,344.00),(3,5,'811651','Johanna','Flores','131212',10.00,124.00),(4,13,'2121','121','2121','121',0.00,0.00),(5,14,'2121','1212','12121','1212',0.00,0.00),(6,15,'2121','1212','12121','1212',0.00,0.00),(7,16,'2121','1212','12121','1212',0.00,0.00),(8,17,'2','2','121','1212',12.00,1212.00),(9,18,'2','2','121','1212',0.00,0.00),(10,19,'2','2','121','1212',0.00,0.00),(11,20,'2121','121','21','121',0.00,0.00),(12,21,'2121','121','21','1212',121.00,21.00),(13,22,'12312312','213','123','12312',121.00,121.00),(14,23,'221','121','2121','2121',1212.00,12.00),(15,24,'221','121','2121','2121',1212.00,12.00),(16,25,'121','121','1212','1212',1212.00,131.00),(17,26,'121','121','1212','1212',1212.00,131.00),(18,27,'3123123213','1232','1321','123123',3123.00,312.00),(19,28,'312','13','12','2312',1212.00,121.00);
        /*!40000 ALTER TABLE `carga_consolidada_cotizaciones_detalle` ENABLE KEYS */;
        UNLOCK TABLES;
        
        --
        -- Table structure for table `carga_consolidada_cotizaciones_cabecera`
        --
        
        DROP TABLE IF EXISTS `carga_consolidada_cotizaciones_cabecera`;
        /*!40101 SET @saved_cs_client     = @@character_set_client */;
        /*!50503 SET character_set_client = utf8mb4 */;
        CREATE TABLE `carga_consolidada_cotizaciones_cabecera` (
          `ID_Cotizacion` int(11) NOT NULL AUTO_INCREMENT,
          `Fe_Creacion` date NOT NULL,
          `N_Cliente` mediumtext DEFAULT NULL,
          `Empresa` mediumtext DEFAULT NULL,
          `Cotizacion` decimal(10,2) DEFAULT NULL,
          `ID_Tipo_Cliente` int(11) NOT NULL,
          `Cotizacion_Status` enum('Pendiente','Cotizado') DEFAULT 'Pendiente',
          `CotizacionCode` char(6) NOT NULL DEFAULT '',
          `created_at` date DEFAULT NULL,
          `Cotizacion_Status_ID` int(11) DEFAULT 1,
          PRIMARY KEY (`ID_Cotizacion`),
          KEY `ID_Tipo_Cliente` (`ID_Tipo_Cliente`),
          CONSTRAINT `carga_consolidada_cotizaciones_cabecera_ibfk_1` FOREIGN KEY (`ID_Tipo_Cliente`) REFERENCES `carga_consolidada_tipo_cliente` (`ID_Tipo_Cliente`)
        ) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4;
        /*!40101 SET character_set_client = @saved_cs_client */;
        
        --
        -- Dumping data for table `carga_consolidada_cotizaciones_cabecera`
        --
        
        LOCK TABLES `carga_consolidada_cotizaciones_cabecera` WRITE;
        /*!40000 ALTER TABLE `carga_consolidada_cotizaciones_cabecera` DISABLE KEYS */;
        INSERT INTO `carga_consolidada_cotizaciones_cabecera` VALUES (1,'2024-05-17','213','21321',NULL,2,'Pendiente','240001',NULL,1),(2,'2024-05-17','Francis Cristofer Torres Cuya','Probussiness',NULL,2,'Pendiente','240002',NULL,2),(3,'2024-05-17','Francis Cristofer Torres Cuya','Probussiness',NULL,2,'Pendiente','240003',NULL,1),(5,'2024-05-20','Johanna Flores',NULL,NULL,2,'Pendiente','240004',NULL,2),(13,'2024-05-20','121 2121','21',NULL,2,'Pendiente','240005',NULL,2),(14,'2024-05-20','1212 12121','121',NULL,2,'Pendiente','240006',NULL,1),(15,'2024-05-20','1212 12121','121',NULL,2,'Pendiente','240007',NULL,1),(16,'2024-05-20','1212 12121','121',NULL,2,'Pendiente','240008',NULL,1),(17,'2024-05-20','2 121','21',NULL,1,'Pendiente','240009',NULL,2),(18,'2024-05-20','2 121','21',NULL,1,'Pendiente','240010',NULL,1),(19,'2024-05-20','2 121','21',NULL,1,'Pendiente','240011',NULL,1),(20,'2024-05-20','121 21','21211',NULL,1,'Pendiente','240012',NULL,1),(21,'2024-05-20','121 21','121',NULL,1,'Pendiente','240013',NULL,1),(22,'2024-05-20','213 123','3123213',NULL,1,'Pendiente','240014',NULL,1),(23,'2024-05-20','121 2121','121',NULL,1,'Pendiente','240015',NULL,1),(24,'2024-05-20','121 2121','121',NULL,1,'Pendiente','240016',NULL,1),(25,'2024-05-20','121 1212','121',NULL,1,'Pendiente','240017',NULL,1),(26,'2024-05-20','121 1212','121',NULL,1,'Pendiente','240018',NULL,1),(27,'2024-05-20','1232 1321','21312321',NULL,1,'Pendiente','240019',NULL,1),(28,'2024-05-20','13 12','3123',NULL,1,'Pendiente','240020',NULL,1);
        /*!40000 ALTER TABLE `carga_consolidada_cotizaciones_cabecera` ENABLE KEYS */;
        UNLOCK TABLES;
        
        --
        -- Table structure for table `carga_consolidada_tipo_cliente`
        --
        
        DROP TABLE IF EXISTS `carga_consolidada_tipo_cliente`;
        /*!40101 SET @saved_cs_client     = @@character_set_client */;
        /*!50503 SET character_set_client = utf8mb4 */;
        CREATE TABLE `carga_consolidada_tipo_cliente` (
          `ID_Tipo_Cliente` int(11) NOT NULL AUTO_INCREMENT,
          `Nombre` varchar(255) NOT NULL,
          PRIMARY KEY (`ID_Tipo_Cliente`)
        ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
        /*!40101 SET character_set_client = @saved_cs_client */;
        
        --
        -- Dumping data for table `carga_consolidada_tipo_cliente`
        --
        
        LOCK TABLES `carga_consolidada_tipo_cliente` WRITE;
        /*!40000 ALTER TABLE `carga_consolidada_tipo_cliente` DISABLE KEYS */;
        INSERT INTO `carga_consolidada_tipo_cliente` VALUES (1,'Nuevo'),(2,'Antiguo');
        /*!40000 ALTER TABLE `carga_consolidada_tipo_cliente` ENABLE KEYS */;
        UNLOCK TABLES;
        /*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
        
        /*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
        /*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
        /*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
        /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
        /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
        /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
        /*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
        
        -- Dump completed on 2024-05-21 12:59:34
        ";
        DB::unprepared($query);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
