# E-commerce Marketplace (API)
<!-- ALL-CONTRIBUTORS-BADGE:START - Do not remove or modify this section -->
[![All Contributors](https://img.shields.io/badge/all_contributors-1-orange.svg?style=flat-square)](#contributors-)
<!-- ALL-CONTRIBUTORS-BADGE:END -->
![](https://img.shields.io/github/issues/leifermendez/ecommerce-api)
![](https://img.shields.io/github/stars/leifermendez/ecommerce-api)

Tienda en línea multiplataforma, la cual cuenta con un sistema de pagos en línea, cobro de comisión por transición, envió de SMS, registro de tiendas entre otras características.

> __¿Eres desarrollador?__ Si te gustaria ayudar a crecer el proyecto tenemos un grupo de [Slack](https://desarrolloah.slack.com/archives/C0133SK41EZ) en donde organizamos la tareas.


![](https://i.imgur.com/FfEhmDv.png)

> Puedes ver algunos de nuestros clientes
- [https://tienda.alterhome.es/](https://tienda.alterhome.es/)
- [https://tienda.mochileros.com.mx/](https://tienda.mochileros.com.mx/)

![](https://i.imgur.com/1KYwHuo.png)

# Requerimientos
> - PHP 7.1 o superior
> - MariaDB 10.2 o superior / MySQL 5.7.8 o superior
> - Conexion HTTPS
> - [Composer](https://getcomposer.org/)
> - [Stripe](https://stripe.com/es/connect): Para el correcto funcionamiento de la plataforma de pago
> - [Twilio](http://twilio.com/): Envio de SMS
> - [Facebook ID](https://developers.facebook.com/apps/): App de facebook para el correcto funcionamiento del login
> - [Google ID](https://console.developers.google.com/?hl=ES): App de google para el correcto funcionamiento del login


# Instalacion

##### Nuevo instalador

Tenemos un nuevo asistente de instalación el cual está en versión "beta" 
pero funciona bien. Una vez cargado los archivos podemos dirigirnos a
 la siguiente ruta `/install`
 
![](https://i.imgur.com/SjAKRUs.gif)

##### Servidor compartido

> __Recuerda__ Puedes ver nuestros video tutoriales de instalación en linea, o podemos instalarlo por ti! [Ver más](https://www.codigoencasa.com/e-commerce-instalacion/)

1. Descargar repositorio `.zip`
![](https://i.imgur.com/8jswcoQ.png)
2. Descomprimir `.zip` en tu computadora
3. Ejecutar `composer clearcache && composer install`
4. ![](https://i.imgur.com/J3DJMCX.png)
5. Volvemos a comprimir en `.zip` asegurandonos de contener la carpeta `vendor`
6. ![](https://i.imgur.com/b78GtQ1.png)
7. Subimos nuestro nuevo archivo comprimido a nuestro servidor y extraes los archivos
8. ![](https://i.imgur.com/vRqic2i.png)
9. Nos dirigimos a `http://TU_SITIO.COM/public/install`
10. ![](https://i.imgur.com/n3mycPE.png)
11. Seguimos los pasos del Wizard
12. ![](https://i.imgur.com/yaeBSQX.png)
13. El sistema verifica nuestro sistema operativo.
14. Verificamos los permisos de nuesto directorio
15. ![](https://i.imgur.com/U0x7p5u.png)
16. Configuramos los accesos a la plataforma de pago
17. ![](https://i.imgur.com/EySFWJZ.png)
18. Configuramos la conexión con la base de datos
19. ![](https://i.imgur.com/s0CHME0.png)
20. Configuramos los valores de usabilidad de nuestro sitio
21. ![](https://i.imgur.com/lHtzIFB.png)
22. Finalmente la configuracion se establecio
23. ![](https://i.imgur.com/scoa2ba.png)


##### 1.2 Servidor dedicado
> __Recuerda__ Puedes ver nuestros video tutoriales de instalación en linea, o podemos instalarlo por ti! [Ver más](https://www.codigoencasa.com/e-commerce-instalacion/)

- Ingresamos via SSH a nuestro servidor
- Clonamos el repositorio `git clone https://github.com/leifermendez/ecommerce-api.git` 
- Ingresamos al directorio creado `ecommerce-api`
- Ejecutamos `composer install`
- Y seguimos los pasos de __Servidor compartido (9)__ en adelante
- 

##### Usurios
Por defecto el sistema crea (3) usuarios
- Admin: `admin@mail.com` Contraseña: `123456`
- Shop: `shop@mail.com` Contraseña: `123456`
- Cliente: `cliente@mail.com` Contraseña: `123456`


### Anotación
- Es importante ingresar el horario de disponibilidad para el buen funcionamiento
- Recuerda agregarle cantidad de inventario

### Contacto
Para dudas información, recomendaciones [codigoencasa.com](https://www.codigoencasa.com/te-ayudamos-con-tu-codigo/)

__Grupo de facebook:__ https://www.facebook.com/groups/163216871776185

__Slack:__ https://desarrolloah.slack.com/archives/C0133SK41EZ

<a href="https://www.buymeacoffee.com/leifermendez" target="_blank"><img src="https://www.buymeacoffee.com/assets/img/custom_images/orange_img.png" alt="Buy Me A Coffee" style="height: 41px !important;width: 174px !important;box-shadow: 0px 3px 2px 0px rgba(190, 190, 190, 0.5) !important;-webkit-box-shadow: 0px 3px 2px 0px rgba(190, 190, 190, 0.5) !important;" ></a>

## Contributors ✨

Thanks goes to these wonderful people ([emoji key](https://allcontributors.org/docs/en/emoji-key)):

<!-- ALL-CONTRIBUTORS-LIST:START - Do not remove or modify this section -->
<!-- prettier-ignore-start -->
<!-- markdownlint-disable -->
<table>
  <tr>
    <td align="center"><a href="https://github.com/yond1994"><img src="https://avatars2.githubusercontent.com/u/47557263?v=4" width="100px;" alt=""/><br /><sub><b>yond1994</b></sub></a><br /><a href="#infra-yond1994" title="Infrastructure (Hosting, Build-Tools, etc)">🚇</a> <a href="https://github.com/leifermendez/ecommerce-api/commits?author=yond1994" title="Tests">⚠️</a> <a href="https://github.com/leifermendez/ecommerce-api/commits?author=yond1994" title="Code">💻</a></td>
  </tr>
</table>

<!-- markdownlint-enable -->
<!-- prettier-ignore-end -->
<!-- ALL-CONTRIBUTORS-LIST:END -->

This project follows the [all-contributors](https://github.com/all-contributors/all-contributors) specification. Contributions of any kind welcome!