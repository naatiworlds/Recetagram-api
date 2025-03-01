# Recetagram - API REST Laravel

[![Review Assignment Due Date](https://classroom.github.com/assets/deadline-readme-button-22041afd0340ce965d47ae6ef1cefeee28c7c493a6346c4f15d667ab976d596c.svg)](https://classroom.github.com/a/gIAr2Q8U)
[![Open in Codespaces](https://classroom.github.com/assets/launch-codespace-2972f46106e565e64193e422d61a12cf1da4916b45550586e14ef0a7c637dd04.svg)](https://classroom.github.com/open-in-codespaces?assignment_repo_id=18415176)

## 📝 Descripción del Proyecto
API RESTful desarrollada con Laravel 10 para la gestión de recetas de cocina. Este proyecto forma parte del módulo de Desarrollo Web en Entorno Servidor y se integrará con un cliente Vue.js.

## 🎯 Objetivos del Proyecto
- API REST completa en Laravel 10
- Integración con frontend Vue.js
- Autenticación y autorización segura
- Documentación completa de la API
- Sistema de persistencia de datos
- Despliegue en producción

## 🚀 Estado del Proyecto
Progreso Total: 45/100 puntos (+ 5 puntos extra posibles por HTTPS)

### Componentes y Puntuación

#### 1. Desarrollo API RESTful (20%) - 10/20
- ✅ Estructura básica implementada
- ✅ Endpoints versionados (v1)
- ⚠️ Pendiente: Completar operaciones CRUD
- ⚠️ Pendiente: Optimización y caché

#### 2. Base de Datos y Persistencia (15%) - 5/15
- ✅ Conexión a base de datos establecida
- ❌ Pendiente: Migraciones completas
- ❌ Pendiente: Modelos y relaciones
- ❌ Pendiente: Seeders

#### 3. Autenticación y Autorización (20%) - 20/20
- ✅ Laravel Sanctum implementado
- ✅ Sistema Login/Logout funcional
- ✅ Protección de rutas
- ✅ Gestión de permisos por usuario

#### 4. Testing - Insomnia/Postman (10%) - 0/10
- ❌ Pendiente: Colección de pruebas
- ❌ Pendiente: Documentación de endpoints
- ❌ Pendiente: Casos de prueba

#### 5. Documentación (10%) - 5/10
- ✅ README básico
- ❌ Pendiente: Documentación OpenAPI/Swagger
- ❌ Pendiente: Guía de uso de la API

#### 6. Despliegue (10%) - 0/10
- ❌ Pendiente: Instrucciones de despliegue
- ❌ Pendiente: Configuración de entorno
- ❌ Pendiente: HTTPS (opcional +5%)

#### 7. Calidad del Código (10%) - 5/10
- ✅ Estructura MVC
- ✅ Patrones de diseño básicos
- ⚠️ Pendiente: Optimización
- ⚠️ Pendiente: Mejores prácticas

## 🛠️ Instalación

[Pendiente: Instrucciones detalladas de instalación]

## 📚 Documentación API

### Endpoints Disponibles

#### Autenticación
```
POST /api/v1/register - Registrar nuevo usuario
POST /api/v1/login    - Iniciar sesión
POST /api/v1/logout   - Cerrar sesión (requiere autenticación)
```

#### Usuarios
```
GET    /api/v1/users          - Listar usuarios (requiere autenticación)
GET    /api/v1/users/{user}   - Obtener usuario específico (requiere autenticación)
POST   /api/v1/users          - Crear usuario (requiere autenticación)
PUT    /api/v1/users/{user}   - Actualizar usuario (requiere autenticación)
DELETE /api/v1/users/{user}   - Eliminar usuario (requiere autenticación)
GET    /api/v1/user           - Obtener usuario autenticado actual
```

Todos los endpoints protegidos requieren un token de autenticación válido proporcionado por Laravel Sanctum.

[Pendiente: Documentación detallada de request/response para cada endpoint]

## 👥 Autor
Natalia Cortés Bernal

## 📄 Licencia
[Pendiente: Información de licencia]
