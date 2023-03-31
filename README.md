# OpenAI-Telegram-Chatbot: Chatbot con OpenAI y Telegram API
Este código es un bot de Telegram que utiliza la API de OpenAI para responder a los mensajes enviados al bot. 

Utiliza **variables de entorno** para cargar las credenciales de la API de OpenAI y el token del bot de Telegram. 

El código define funciones para manejar las conversaciones previas y guardarlas en **archivos JSON**.

También define funciones para **enviar mensajes a través de la API de Telegram** y verificar si el chat de Telegram es un chat habilitado. 

Por último, el código define la función chatGPT3Turbo, que utiliza la **API de OpenAI** para generar una respuesta al mensaje enviado al bot, basada en las conversaciones previas almacenadas en el archivo JSON correspondiente al chat.

## Instrucciones de uso
Aquí te explicamos cómo usar el chatbot desarrollado con OpenAI y Telegram API:

1. Primero, clona el repositorio en tu servidor (asegúrate de tener HTTPS activado).
2. A continuación, crea un nuevo bot de Telegram siguiendo las [instrucciones oficiales](https://core.telegram.org/bots#how-do-i-create-a-bot) mediante [@botfather](https://t.me/botfather) y guarda el token que se te proporcionará.
3. Accede a la [API de OpenAI](https://platform.openai.com/account/api-keys) y genera un token.Guárdelo también.
4. Cambie el nombre del archivo de variables de entorno .`env.demo` a `.env` y añade el token obtenidos anteriormente en las variables `TELEGRAM_BOT_TOKEN` y `OPENAI_API_KEY` respectivamente.
5. Configura Telegram para usar Webhooks, tal y como se indica en su sitio web, creando una petición POST a https://api.telegram.org/bot[TOKEN]/setWebhook y añadiendo en el cuerpo de la petición (Para ello puede utilizar [Postman](https://www.postman.com/downloads/)):
```
{
    "url":"https://[URL]/index.php"
}
```
6. Habla con tu bot a través de Telegram y comprueba que los comandos básicos funcionan correctamente.
7. Obtén el `ChatId` de tu conversación y añádelo al archivo `.env` que modificaste anteriormente.
8. ¡Listo! Ahora puedes empezar a gestionar quién puede obtener respuestas de la inteligencia artificial y compartirlo con tus amigos de forma gratuita. ¡Disfruta!

### Añadir comandos en Telegram
También esta la opción de añadir comandos en Telegram, lo que permitirá ver las opciones disponibles en un menú. Para ello, una vez seleccionamos nuestro bot en el chat con [@botfather](https://t.me/botfather), podemos añadir los siguientes comandos:
```
start - Inicia el bot.
enable_global_chats - Habilita el uso del bot en todos los chats.
disable_global_chats - Deshabilita el uso del bot en todos los chats.
get_chat_id - Obtiene el ID de chat.
clear_conversations - Limpia la lista de conversaciones.
add - Añade un chat a la lista de chats habilitados.
delete - Elimina un chat de la lista de chats habilitados.
help - Muestra esta ayuda.
```

## Finalidad educativa
El proyecto tiene como objetivo proporcionar a los estudiantes de Arquitectura y Tecnología de Sistemas Web y Multimedia y a los alumnos de ciclos formativos de desarrollo de aplicaciones web en el centro educativo [***Gimbernat***](http://eug.es/), una herramienta práctica para aprender sobre el desarrollo de Chatbots utilizando tecnologías como OpenAI y Telegram API. A través de este proyecto, los estudiantes podrán familiarizarse con los conceptos y técnicas necesarios para crear Chatbots y explorar su potencial en el ámbito de la comunicación y el servicio al cliente.

En primer lugar, el proyecto permite a los estudiantes aprender sobre la **creación y el desarrollo de un Chatbot**, lo que les brinda una valiosa experiencia práctica en el desarrollo de software conversacional. Además, al utilizar tecnologías como OpenAI y Telegram API, los estudiantes tienen la oportunidad de trabajar con **herramientas avanzadas y actuales en el desarrollo de aplicaciones** de chat.

En segundo lugar, el proyecto también les enseña a los alumnos sobre la integración de diferentes tecnologías y servicios, lo que les **ayuda a comprender cómo funcionan los sistemas tecnológicos en conjunto**. Por ejemplo, aprenderán cómo se comunican OpenAI y Telegram API y cómo se pueden integrar para crear un **Chatbot funcional**.

Esta es una valiosa experiencia práctica en el desarrollo de aplicaciones de chat utilizando herramientas avanzadas y actuales. Se documenta todo el proceso en un README.md utilizando el lenguaje de marcado Markdown y la plataforma GitHub para el control de versiones, lo que brinda a los alumnos una experiencia práctica adicional en la utilización de estas herramientas. Además, el proyecto permite aprender sobre la integración de diferentes tecnologías y servicios y les brinda la oportunidad de aplicar estos conocimientos en la creación de aplicaciones reales y útiles.

## Licencia

Este proyecto está licenciado bajo la [Licencia Creative Commons Atribución-CompartirIgual 4.0 Internacional](https://creativecommons.org/licenses/by-sa/4.0/deed.es).

[![Licencia de Creative Commons](https://i.creativecommons.org/l/by-sa/4.0/88x31.png)](https://creativecommons.org/licenses/by-sa/4.0/deed.es)

## Contribuciones

Si este proyecto te ha sido útil y te gustaría ver más proyectos similares, considera invitarme a un café :) para ayudarme a continuar desarrollando y mejorando el código. ¡Tu apoyo es muy apreciado y ayuda a mantener vivo este proyecto!

[![PayPal](https://www.paypalobjects.com/webstatic/mktg/logo/AM_SbyPP_mc_vs_dc_ae.jpg)](https://paypal.me/fmesasc?country.x=ES&locale.x=es_ES)