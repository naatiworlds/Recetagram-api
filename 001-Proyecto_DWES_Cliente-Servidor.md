# **Proyecto DWES: API RESTful con Laravel 10**

## **Descripción del Proyecto**
Este proyecto consiste en el desarrollo de una **API RESTful en Laravel 10** que servirá como **backend** para la aplicación cliente que se desarrollará en el módulo **Desarrollo Web en Entorno Cliente (DWEC)**. La API proporcionará las funcionalidades necesarias para gestionar los datos y la lógica de negocio de la aplicación, permitiendo la comunicación con el frontend a través de peticiones HTTP.

El servidor debe implementar operaciones **CRUD (Crear, Leer, Actualizar, Eliminar)**, manejar **autenticación y autorización**, y garantizar la seguridad de los datos. La base de datos utilizada podrá ser **PostgreSQL o MySQL**, y se podrá emplear **Redis o la caché de Laravel** opcionalmente para optimizar el rendimiento.

## **Objetivos**
- Implementar una API REST en **Laravel 10** (opcional Laravel 11).
- Integrar la API con el frontend desarrollado en **DWEC**.
- Implementar **autenticación y autorización** para restringir el acceso a los datos.
- Documentar la API y proporcionar una suite de pruebas en **Insomnia/Postman**.
- Garantizar la persistencia de datos con **PostgreSQL o MySQL**.
- Desplegar la API en un entorno accesible, con opción de configurar **HTTPS**.

---

## **Requisitos Técnicos**
1. **API RESTful en Laravel 10** con soporte para autenticación y autorización.
2. **Base de datos relacional** (PostgreSQL o MySQL).
3. **Autenticación obligatoria** con **Laravel Sanctum o JWT**, evitando modificaciones de datos entre usuarios.
4. **Documentación API** con OpenAPI/Swagger y una colección de pruebas en **Insomnia/Postman**.
5. **Separación entre lógica de negocio y presentación**.
6. **Manejo adecuado de errores y validaciones**.
7. **Instrucciones de despliegue**, con opción de configuración HTTPS.
8. **El servidor debe integrarse con el cliente desarrollado en DWEC**.

---

## **Formato de Entrega**
Cada estudiante deberá:
- Subir el código fuente a **GitHub** en un repositorio privado dentro de **GitHub Classroom**.
- Incluir un **README.md** con:
  - Descripción del proyecto y sus funcionalidades.
  - Instrucciones detalladas para instalación y uso.
  - Enlace a la documentación de la API.
- Subir la suite de pruebas en **Insomnia/Postman**.

---

## **Criterios de Evaluación**

| **Parte del Proyecto**                   | **Peso en la Nota** | **Resultados de Aprendizaje Evaluados** | **Criterios de Evaluación** |
|-------------------------------------------|---------------------|-----------------------------------------|----------------------------|
| **Desarrollo de la API RESTful** (CRUD y lógica de negocio) | **20%** | **5. Desarrolla aplicaciones Web separando código de presentación y lógica de negocio.** <br> **6. Desarrolla aplicaciones de acceso a almacenes de datos.** <br> **7. Desarrolla servicios Web analizando su funcionamiento.** | - Se han identificado y aplicado mecanismos de separación de lógica de negocio. <br> - Se han creado aplicaciones que establecen conexiones con bases de datos. <br> - Se ha programado un servicio Web y verificado su funcionamiento. |
| **Base de datos y persistencia de datos** | **15%** | **6. Desarrolla aplicaciones de acceso a almacenes de datos.** | - Se han creado aplicaciones que establecen conexiones con bases de datos. <br> - Se han recuperado y publicado datos en la aplicación Web. <br> - Se han utilizado transacciones para mantener la consistencia de la información. |
| **Autenticación y autorización (OBLIGATORIO)** | **20%** | **4. Desarrolla aplicaciones Web con mecanismos de autentificación.** <br> **6. Desarrolla aplicaciones de acceso a almacenes de datos.** | - Se han implementado mecanismos de autenticación seguros (Laravel Sanctum o JWT). <br> - Se ha aplicado autorización para que los usuarios no puedan modificar datos de otros usuarios. <br> - Se han implementado roles o permisos para restringir acciones en la API. |
| **Pruebas con Insomnia/Postman** | **10%** | **7. Desarrolla servicios Web analizando su funcionamiento.** | - Se ha verificado el funcionamiento de la API mediante pruebas automatizadas o manuales. <br> - Se han cubierto casos de éxito y error en la suite de pruebas. |
| **Documentación (README y documentación API)** | **10%** | **7. Desarrolla servicios Web analizando su funcionamiento.** <br> **5. Desarrolla aplicaciones Web separando código de presentación y lógica de negocio.** | - Se ha documentado la API con instrucciones claras para su uso. <br> - Se ha documentado la arquitectura y configuración del proyecto. |
| **Despliegue y configuración del entorno (Opcional HTTPS)** | **10%** | **6. Desarrolla aplicaciones de acceso a almacenes de datos.** <br> **7. Desarrolla servicios Web analizando su funcionamiento.** | - Se han incluido instrucciones claras para la instalación y configuración del servidor. <br> - Se ha probado que la API puede ejecutarse sin errores en un entorno de producción o desarrollo. <br> - **Si se implementa HTTPS con certificado, se sumará hasta un 5% extra a la nota final.** |
| **Estructura y calidad del código** | **10%** | **5. Desarrolla aplicaciones Web separando código de presentación y lógica de negocio.** | - Se han aplicado principios de programación orientada a objetos. <br> - Se han utilizado estructuras adecuadas para mejorar la legibilidad y mantenimiento del código. |

---

## **Notas Importantes**
- **El servidor debe ser funcional**. Si no se puede ejecutar, se descontará la nota correspondiente.
- **Autenticación y autorización son obligatorias**. No cumplir con este requisito resultará en una **calificación de 0** en esa parte.
- **El despliegue con HTTPS es opcional, pero sumará hasta un 5% adicional** si se implementa correctamente.
- **El servidor debe integrarse con el cliente desarrollado en DWEC**.

---