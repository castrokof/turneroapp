# Manual de Usuario Final - Sistema Turnero

## Tabla de Contenidos

1. [Introduccion](#1-introduccion)
2. [Para Clientes - Uso del Kiosco](#2-para-clientes---uso-del-kiosco)
3. [Para Agentes - Panel de Atencion](#3-para-agentes---panel-de-atencion)
4. [Pantalla de Espera (TV)](#4-pantalla-de-espera-tv)
5. [Preguntas Frecuentes](#5-preguntas-frecuentes)

---

## 1. Introduccion

El Sistema Turnero es una aplicacion de gestion de colas que permite:

- **A los clientes**: Obtener un turno de forma rapida y ordenada
- **A los agentes**: Llamar y atender turnos de manera eficiente
- **A todos**: Ver el estado de los turnos en pantallas publicas

### Prioridades de Atencion

El sistema maneja prioridades automaticas:

| Prioridad | Descripcion | Deteccion |
|-----------|-------------|-----------|
| Emergencia | Casos urgentes | Manual por admin |
| Prioritario | Adultos mayores, embarazadas, personas con discapacidad | Automatica |
| Con Cita | Clientes con cita previa | Al generar turno |
| Normal | Clientes regulares | Por defecto |

---

## 2. Para Clientes - Uso del Kiosco

### 2.1 Obtener un Turno

#### Paso 1: Acercarse al Kiosco
Dirijase a la pantalla tactil del kiosco ubicada en la entrada.

#### Paso 2: Seleccionar Servicio
1. En la pantalla de bienvenida, vera los servicios disponibles
2. Toque el boton del servicio que necesita (por ejemplo: "Atencion General", "Pagos", "Consultas")

#### Paso 3: Identificarse (Opcional)
- **Si ya es cliente registrado**: Ingrese su numero de documento
- **Si es nuevo**: Puede ingresar sus datos o continuar sin registro

#### Paso 4: Confirmar Prioridad
Si aplica alguna prioridad (adulto mayor, embarazada, discapacidad), seleccionela.

#### Paso 5: Recibir Ticket
1. El sistema generara su numero de turno (ejemplo: **A-025**)
2. Se imprimira un ticket con:
   - Numero de turno
   - Tipo de servicio
   - Fecha y hora
   - Cantidad de personas antes de usted
   - Tiempo estimado de espera

### 2.2 Esperar su Turno

1. Tome asiento en la sala de espera
2. Observe la **pantalla TV** donde aparecen los turnos llamados
3. Cuando vea su numero, dirijase a la ventanilla indicada

### 2.3 Cancelar un Turno

Si necesita retirarse antes de ser atendido:
1. Acerquese a un agente o administrador
2. Proporcione su numero de turno
3. El turno sera cancelado

### 2.4 Ejemplo de Ticket

```
================================
      SALUD MEDCOL
================================
   Fecha: 27/01/2026 10:35

   Servicio: Atencion General

        *** A-025 ***

   Prioridad: PRIORITARIO
   Ventanilla: Por asignar
   Personas antes: 3
   Espera estimada: 15 min

   Gracias por su visita
================================
```

---

## 3. Para Agentes - Panel de Atencion

### 3.1 Iniciar Sesion

1. Abra el navegador y vaya a la direccion del sistema
2. Ingrese su **usuario** y **contrasena**
3. Haga clic en "Iniciar Sesion"

### 3.2 Seleccionar Ventanilla

Al ingresar al panel de agente:

1. Vera una lista de ventanillas disponibles
2. Seleccione su ventanilla asignada (ejemplo: "Ventanilla 1")
3. El sistema confirmara la asignacion

**Nota**: Solo puede tener una ventanilla a la vez.

### 3.3 Panel Principal

El panel de agente muestra:

```
+----------------------------------+
|  VENTANILLA 1                    |
|  Estado: Activa                  |
+----------------------------------+
|                                  |
|  TURNO ACTUAL: A-025             |
|  Cliente: Juan Perez             |
|  Servicio: Atencion General      |
|  Prioridad: Prioritario          |
|                                  |
|  [Iniciar] [Completar] [Ausente] |
|                                  |
+----------------------------------+
|  TURNOS EN ESPERA (5)            |
|  1. A-026 - Normal               |
|  2. A-027 - Prioritario          |
|  3. A-028 - Normal               |
+----------------------------------+
```

### 3.4 Operaciones Basicas

#### Llamar Siguiente Turno
1. Haga clic en **"Llamar Siguiente"**
2. El sistema seleccionara el turno con mayor prioridad
3. Se anunciara en la pantalla TV
4. El cliente debe acercarse a su ventanilla

#### Llamar Turno Especifico
1. En la lista de turnos pendientes
2. Haga clic en el turno deseado
3. Seleccione **"Llamar"**

#### Rellamar Cliente
Si el cliente no se acerca:
1. Haga clic en **"Rellamar"**
2. Se anunciara nuevamente en la TV

#### Iniciar Atencion
Cuando el cliente llegue a su ventanilla:
1. Haga clic en **"Iniciar Servicio"**
2. El cronometro de atencion comenzara

#### Completar Atencion
Al finalizar el servicio:
1. Haga clic en **"Completar"**
2. Opcionalmente agregue notas
3. El turno se marcara como finalizado

#### Marcar Ausente
Si el cliente no aparece despues de llamarlo:
1. Haga clic en **"Marcar Ausente"**
2. Ingrese una razon (opcional)
3. El turno se cerrara como ausente

### 3.5 Transferir Turno

Si el cliente necesita otro servicio:

1. Haga clic en **"Transferir"**
2. Seleccione la ventanilla destino
3. Ingrese la razon de la transferencia
4. Confirme la operacion

El turno volvera a la cola de espera de la nueva ventanilla.

### 3.6 Pausar/Reanudar Ventanilla

#### Para Pausar (descanso, almuerzo):
1. Haga clic en **"Pausar Ventanilla"**
2. La ventanilla dejara de recibir turnos nuevos
3. Si tiene un turno en atencion, podra finalizarlo

#### Para Reanudar:
1. Haga clic en **"Reanudar Ventanilla"**
2. Volvera a recibir turnos

### 3.7 Abandonar Ventanilla

Al finalizar su turno de trabajo:

1. Complete cualquier turno en atencion
2. Haga clic en **"Abandonar Ventanilla"**
3. La ventanilla quedara libre para otro agente

**Importante**: No puede abandonar si tiene un turno en proceso.

### 3.8 Estadisticas del Dia

En el panel puede ver:
- Turnos atendidos hoy
- Tiempo promedio de atencion
- Turnos en espera
- Proximos turnos

---

## 4. Pantalla de Espera (TV)

### 4.1 Que Muestra la Pantalla

La pantalla publica muestra:

```
+------------------------------------------------+
|                                                |
|     SALUD MEDCOL           [LOGO]              |
|                                                |
+------------------------------------------------+
|                          |                     |
|    TURNO LLAMADO         |    VENTANILLAS      |
|                          |                     |
|      *** A-025 ***       |  V1: A-025 Llamando |
|                          |  V2: B-012 Atendiendo|
|    Ventanilla: 1         |  V3: Disponible     |
|                          |  V4: Pausada        |
|                          |                     |
+------------------------------------------------+
|   Proximos: A-026, A-027, A-028, B-013, A-029  |
+------------------------------------------------+
|   "Bienvenidos - Por favor espere su turno"    |
+------------------------------------------------+
```

### 4.2 Elementos de la Pantalla

| Seccion | Descripcion |
|---------|-------------|
| Turno Llamado | Numero grande del turno que debe pasar |
| Ventanilla | Numero de ventanilla donde debe ir |
| Estado Ventanillas | Que hace cada ventanilla |
| Proximos | Lista de turnos que siguen |
| Mensaje | Informacion del negocio |
| Video | Puede mostrar un video informativo |

### 4.3 Anuncios de Audio

Cuando se llama un turno:
- Suena una campana o tono
- Una voz anuncia: **"Turno A-025, pase a Ventanilla 1"**

---

## 5. Preguntas Frecuentes

### Para Clientes

**P: Perdi mi ticket, que hago?**
R: Acerquese a un agente e indique sus datos. Pueden buscar su turno en el sistema.

**P: Mi turno fue llamado pero no alcance a llegar, que pasa?**
R: Acerquese rapidamente a la ventanilla. Si fue marcado ausente, solicite un nuevo turno.

**P: Puedo saber cuanto tiempo falta para mi turno?**
R: El ticket impreso tiene un estimado. Tambien puede contar los turnos antes del suyo en la pantalla.

**P: Por que alguien con numero mayor paso antes que yo?**
R: Puede tener prioridad (adulto mayor, embarazada, discapacidad) o estar en una cola de servicio diferente.

### Para Agentes

**P: No puedo llamar turnos, que hago?**
R: Verifique que:
1. Haya seleccionado una ventanilla
2. La ventanilla no este pausada
3. Haya turnos pendientes para su servicio

**P: El sistema no responde, que hago?**
R:
1. Espere unos segundos
2. Refresque la pagina (F5)
3. Si persiste, contacte a TI

**P: Un cliente necesita un servicio que no atiendo, que hago?**
R: Use la funcion "Transferir" para enviarlo a la ventanilla correcta.

**P: Como veo mis estadisticas del dia?**
R: En el panel de agente, en la seccion superior derecha aparecen sus estadisticas.

**P: Puedo atender a alguien sin turno?**
R: No. Todos los clientes deben generar turno para mantener el orden y las estadisticas.

---

## Soporte

Si tiene problemas con el sistema:

1. **Agentes**: Contacten al administrador o equipo de TI
2. **Clientes**: Soliciten ayuda al personal de atencion

---

*Manual version 1.0 - Sistema Turnero*
*Ultima actualizacion: Enero 2026*
