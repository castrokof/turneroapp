# Turnero TV - Aplicación Android

Aplicación Android para mostrar la pantalla de turnos en televisores.

## Características

- Pantalla completa sin barras
- Mantiene la pantalla siempre encendida
- Permite reproducir sonido automáticamente al llamar turnos
- Guarda la configuración del servidor
- Compatible con Android TV y tablets

## Opción 1: Compilar con Android Studio

1. Instala [Android Studio](https://developer.android.com/studio)
2. Abre el proyecto `android-tv-app`
3. Conecta tu dispositivo Android o usa un emulador
4. Haz clic en "Build" > "Build Bundle(s) / APK(s)" > "Build APK(s)"
5. El APK estará en `app/build/outputs/apk/debug/`

## Opción 2: Usar un generador online (más fácil)

1. Ve a [AppsGeyser](https://appsgeyser.com) o [WebViewGold](https://www.webviewgold.com)
2. Ingresa la URL: `http://TU_IP/display/tv`
3. Configura:
   - Pantalla completa: Sí
   - Orientación: Landscape (horizontal)
   - Mantener pantalla encendida: Sí
4. Genera y descarga el APK

## Opción 3: Usar Fully Kiosk Browser (recomendado para TV)

1. Descarga [Fully Kiosk Browser](https://play.google.com/store/apps/details?id=de.ozerov.fully) desde Play Store
2. Configura la URL: `http://TU_IP/display/tv`
3. Activa:
   - Kiosk Mode
   - Keep Screen On
   - Autoplay Media

## Configuración en la App

1. Al abrir la app, ingresa la IP del servidor (ej: `192.168.1.100`)
2. Presiona "Conectar"
3. La app cargará automáticamente `/display/tv`
4. Para cambiar la configuración, presiona el botón BACK del control remoto

## Notas

- El servidor Laravel debe estar accesible en la red local
- Asegúrate de que el firewall permita conexiones en el puerto 80
- Para que el sonido funcione, el navegador/app debe permitir autoplay de audio
