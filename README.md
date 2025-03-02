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
Progreso Total: 50/100 puntos (+ 5 puntos extra posibles por HTTPS)

### Componentes y Puntuación

#### 1. Desarrollo API RESTful (20%) - 12/20
- ✅ Estructura básica implementada
- ✅ Endpoints versionados (v1)
- ✅ Operaciones CRUD para usuarios
- ✅ Operaciones CRUD parciales para posts
- ⚠️ Pendiente: Completar operaciones para ingredientes
- ⚠️ Pendiente: Optimización y caché

#### 2. Base de Datos y Persistencia (15%) - 8/15
- ✅ Conexión a base de datos establecida
- ✅ Migraciones básicas implementadas
- ✅ Modelos principales creados
- ⚠️ Pendiente: Relaciones completas
- ❌ Pendiente: Seeders

#### 3. Autenticación y Autorización (20%) - 20/20
- ✅ Laravel Sanctum implementado correctamente
- ✅ Sistema Login/Logout funcional
- ✅ Protección de rutas implementada
- ✅ Gestión de permisos por usuario
- ✅ Middleware de autenticación configurado

#### 4. Testing - Insomnia/Postman (10%) - 0/10
- ❌ Pendiente: Colección de pruebas
- ❌ Pendiente: Documentación de endpoints
- ❌ Pendiente: Casos de prueba

#### 5. Documentación (10%) - 5/10
- ✅ README con estructura clara
- ✅ Documentación básica de endpoints
- ❌ Pendiente: Documentación OpenAPI/Swagger
- ⚠️ Pendiente: Completar guía de uso

#### 6. Despliegue (10%) - 0/10
- ❌ Pendiente: Instrucciones de despliegue
- ❌ Pendiente: Configuración de entorno
- ❌ Pendiente: HTTPS (opcional +5%)

#### 7. Calidad del Código (10%) - 5/10
- ✅ Estructura MVC implementada
- ✅ Patrones de diseño básicos aplicados
- ⚠️ Pendiente: Optimización
- ⚠️ Pendiente: Implementar más mejores prácticas

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

#### Posts
```
GET    /api/v1/posts          - Listar posts (público)
GET    /api/v1/posts/{post}   - Obtener post específico (público)
POST   /api/v1/posts          - Crear post (requiere autenticación)
PUT    /api/v1/posts/{post}   - Actualizar post (requiere autenticación)
DELETE /api/v1/posts/{post}   - Eliminar post (requiere autenticación)
```

Todos los endpoints protegidos requieren un token de autenticación válido proporcionado por Laravel Sanctum.

[Pendiente: Documentación detallada de request/response para cada endpoint]

## 👥 Autor
Natalia Cortés Bernal

## 📄 Licencia
[Pendiente: Información de licencia]
