# Documentación de la API - Subida de Posts

## Introducción

Esta documentación describe cómo interactuar con la API para subir un post, incluyendo el tratamiento de imágenes y la estructura de los datos requeridos.

## Endpoints

### 1. Crear un Post

- **Método:** `POST`
- **URL:** `/api/v1/posts`
- **Autenticación:** Requiere token Bearer.

### 2. Estructura de la Solicitud

La solicitud debe contener los siguientes campos:

| Campo       | Tipo     | Requerido | Descripción                                      |
|-------------|----------|-----------|--------------------------------------------------|
| title       | string   | Sí        | Título del post.                                 |
| description | string   | Sí        | Descripción del post.                            |
| imagen      | file     | Sí        | Archivo de imagen (jpeg, png, jpg, gif).        |
| ingredients  | string   | Sí        | JSON que representa los ingredientes.            |

#### Ejemplo de Cuerpo de Solicitud
```json
{
"title": "Ejemplo de Post",
"description": "Descripción del post desde el cliente.",
"imagen": "archivo.png", // Este campo se envía como parte de FormData
"ingredients": "[{\"name\": \"Ingrediente 1\", \"quantity\": \"1 taza\"}, {\"name\": \"Ingrediente 2\", \"quantity\": \"2 cucharadas\"}]"
}
```

### 3. Tratamiento de la Imagen en el Cliente

Para subir una imagen desde un cliente (por ejemplo, usando Vue.js), sigue estos pasos:

1. **Captura del Archivo de Imagen:**
   Asegúrate de que el archivo de imagen se capture correctamente en tu componente Vue.

   ```javascript
   handleFileUpload(event) {
       this.post.imagen = event.target.files[0]; // Captura el archivo de imagen
   }
   ```

2. **Creación de FormData:**
   Utiliza `FormData` para enviar los datos, incluyendo la imagen.

   ```javascript
   async submitPost() {
       const formData = new FormData();
       formData.append('title', this.post.title);
       formData.append('description', this.post.description);
       formData.append('imagen', this.post.imagen); // Asegúrate de que este campo esté presente
       formData.append('ingredients', this.post.ingredients);

       try {
           const response = await axios.post('http://localhost:8000/api/v1/posts', formData, {
               headers: {
                   'Content-Type': 'multipart/form-data',
                   'Authorization': `Bearer ${localStorage.getItem('token')}` // Asegúrate de tener el token almacenado
               }
           });
           this.message = response.data.message;
       } catch (error) {
           this.message = 'Error al crear el post: ' + error.response.data.message;
       }
   }
   ```

### 4. Respuesta de la API

Si la creación del post es exitosa, recibirás una respuesta similar a esta:

```json
{
    "status": "success",
    "data": {
        "id": 1,
        "title": "Ejemplo de Post",
        "description": "Descripción del post desde el cliente.",
        "imagen": "ruta/a/la/imagen.jpg",
        "ingredients": [
            {
                "name": "Ingrediente 1",
                "quantity": "1 taza"
            },
            {
                "name": "Ingrediente 2",
                "quantity": "2 cucharadas"
            }
        ],
        "user_id": 1,
        "created_at": "2024-03-04T00:00:00.000000Z",
        "updated_at": "2024-03-04T00:00:00.000000Z"
    },
    "message": "Post created successfully"
}
```

### 5. Errores Comunes

- **422 Unprocessable Entity:** Este error puede ocurrir si falta algún campo requerido o si el formato de los datos es incorrecto. Asegúrate de que todos los campos estén presentes y en el formato correcto.

## Conclusión

Esta documentación proporciona una guía clara sobre cómo interactuar con la API para subir un post, incluyendo el tratamiento de imágenes. Asegúrate de seguir los pasos y ejemplos proporcionados para evitar errores comunes.

```json
{
"status": "success",
"data": {
"id": 1,
"title": "Ejemplo de Post",
"description": "Descripción del post desde el cliente.",
"imagen": "ruta/a/la/imagen.jpg",
"ingredients": [
{
"name": "Ingrediente 1",
"quantity": "1 taza"
},
{
"name": "Ingrediente 2",
"quantity": "2 cucharadas"
}
],
"user_id": 1,
"created_at": "2024-03-04T00:00:00.000000Z",
"updated_at": "2024-03-04T00:00:00.000000Z"
},
"message": "Post created successfully"
}
```


### 5. Errores Comunes

- **422 Unprocessable Entity:** Este error puede ocurrir si falta algún campo requerido o si el formato de los datos es incorrecto. Asegúrate de que todos los campos estén presentes y en el formato correcto.

## Conclusión

Esta documentación proporciona una guía clara sobre cómo interactuar con la API para subir un post, incluyendo el tratamiento de imágenes. Asegúrate de seguir los pasos y ejemplos proporcionados para evitar errores comunes.