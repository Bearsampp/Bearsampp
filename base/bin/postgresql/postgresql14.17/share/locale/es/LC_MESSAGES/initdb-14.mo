��    �      �  �   �
      �  R   �     �  
             .  @   E  `   �  W   �  W   ?  7   �    �  A   �  E     5   ^  J   �  ?   �       6   ;  P   r  C   �  :     Q   B  5   �  ]   �  4   (  B   ]  H   �  G   �  >   1  G   p  4   �  9   �  3   '  ?   [  (   �     �  /   �       I     "   b  !   �  �   �  d   -     �     �  �   �  O   F  R   �  K   �     5  _   N     �     �  <   �  ;   !  �   ]  @   �  ;   /    k  u   |  j   �  _   ]  s   �  &   1      X   t   `   0   �   /   !  �   6!  �   �!  *   a"  A   �"     �"  @   �"  /   %#     U#  &   d#  0   �#  .   �#  -   �#     $     0$  #   B$     f$  '   �$  &   �$  (   �$  2   �$  "   0%  #   S%  1   w%  '   �%  "   �%      �%  0   &  A   F&     �&  7   �&  $   �&  (   '  +   ,'  +   X'  !   �'  (   �'  (   �'     �'  ,   (  :   B(     }(  !   �(  *   �(  %   �(  +   )  &   7)     ^)  $   x)  8   �)     �)  )   �)     *  %   ;*  !   a*     �*     �*  1   �*  &   �*  5   +     J+     \+     d+  *   �+  +   �+     �+  !   �+     ,     $,     =,  0   ],  0   �,  ,   �,  7   �,     $-     8-  B   Q-  .   �-     �-  K   �-     .     3.     7.     E.     T.  >   p.  -   �.     �.  '   �.  (   !/     J/     g/  &   �/  %   �/      �/  3   �/     '0  D   :0  =   0  E   �0  +   1     /1  /   I1     y1  (   �1  	   �1  �  �1  V   s3     �3     �3     �3     4  P   4  �   l4  �   �4  �   o5  =   �5  H  06  y   y7  O   �7  5   C8  P   y8  K   �8     9  7   39  z   k9  R   �9  D   9:  N   ~:  E   �:  n   ;  @   �;  D   �;  M   <  :   V<  ?   �<  �   �<  P   S=  ;   �=  7   �=  K   >  /   d>     �>  7   �>     �>  O   �>  G   C?  D   �?  �   �?  �   }@     "A  /   :A  �   jA  H   �A  D   AB  H   �B     �B  y   �B     iC  #   �C  ?   �C  B   �C  �   /D  F   �D  C   E  G  VE  �   �F  v   'G  p   �G  x   H  3   �H     �H  �   �H  =   II  >   �I  �   �I  �   KJ  1   �J  G   K  "   SK  N   vK  5   �K     �K  1   L  5   IL  5   L  3   �L     �L     M  +   M  (   CM  2   lM  +   �M  -   �M  9   �M  )   3N  )   ]N  ;   �N  0   �N  '   �N  ,   O  W   IO  p   �O  1   P  N   DP  ,   �P  /   �P  <   �P  3   -Q  )   aQ  0   �Q  5   �Q  &   �Q  :   R  E   TR  !   �R  (   �R  :   �R  /    S  7   PS  6   �S  !   �S  /   �S  H   T  +   ZT  8   �T  )   �T  (   �T  '   U     :U     XU  ?   tU  0   �U  J   �U     0V     LV  (   TV  6   }V  8   �V  *   �V  ?   W     XW     `W  *   |W  7   �W  9   �W  ?   X  D   YX     �X  6   �X  N   �X  O   DY     �Y  c   �Y  (   Z     5Z     <Z     LZ  -   ]Z  d   �Z  .   �Z  &   [  4   F[  -   {[  (   �[  ,   �[  0   �[  /   0\  /   `\  B   �\     �\  v   �\  g   ]]  ]   �]  ?   #^  $   c^  E   �^     �^  @   �^     $_             �   �       6           M       �   �   3   �   	      ~   I   �       4       :              �   �   '   �       R       Y   b   ]      �   �       �                �   &   �      n       
   P           B   D   A   V   �   �   C   t   �   d   %   �       T      �   �   p   k   j   @   \   �       !   �               7   m   E   v   =          Z      �       )      f   �   ;       x   �   {       0   1      }   w   �       �   �       e   ,   �       h   *          W       �      �       �      <   �   a                                     q   �      G   J      i   >   �   $   z   �   �   �         2       U          H           |       Q           r       `          �      g       +       K       _   u   -      c       F   L       .      (   9   X      �   S   �           ^   s          [       /   O       8   ?   �   l   #   o   �   "   �   5   y   �       N   �   �           
If the data directory is not specified, the environment variable PGDATA
is used.
 
Less commonly used options:
 
Options:
 
Other options:
 
Report bugs to <%s>.
 
Success. You can now start the database server using:

    %s

 
Sync to disk skipped.
The data directory might become corrupt if the operating system crashes.
       --auth-host=METHOD    default authentication method for local TCP/IP connections
       --auth-local=METHOD   default authentication method for local-socket connections
       --discard-caches      set debug_discard_caches=1
       --lc-collate=, --lc-ctype=, --lc-messages=LOCALE
      --lc-monetary=, --lc-numeric=, --lc-time=LOCALE
                            set default locale in the respective category for
                            new databases (default taken from environment)
       --locale=LOCALE       set default locale for new databases
       --no-instructions     do not print instructions for next steps
       --no-locale           equivalent to --locale=C
       --pwfile=FILE         read password for the new superuser from file
       --wal-segsize=SIZE    size of WAL segments, in megabytes
   %s [OPTION]... [DATADIR]
   -?, --help                show this help, then exit
   -A, --auth=METHOD         default authentication method for local connections
   -E, --encoding=ENCODING   set default encoding for new databases
   -L DIRECTORY              where to find the input files
   -N, --no-sync             do not wait for changes to be written safely to disk
   -S, --sync-only           only sync data directory
   -T, --text-search-config=CFG
                            default text search configuration
   -U, --username=NAME       database superuser name
   -V, --version             output version information, then exit
   -W, --pwprompt            prompt for a password for the new superuser
   -X, --waldir=WALDIR       location for the write-ahead log directory
   -d, --debug               generate lots of debugging output
   -g, --allow-group-access  allow group read/execute on data directory
   -k, --data-checksums      use data page checksums
   -n, --no-clean            do not clean up after errors
   -s, --show                show internal settings
  [-D, --pgdata=]DATADIR     location for this database cluster
 "%s" is not a valid server encoding name %s home page: <%s>
 %s initializes a PostgreSQL database cluster.

 %s() failed: %m Check your installation or specify the correct path using the option -L.
 Data page checksums are disabled.
 Data page checksums are enabled.
 Encoding "%s" implied by locale is not allowed as a server-side encoding.
The default database encoding will be set to "%s" instead.
 Encoding "%s" is not allowed as a server-side encoding.
Rerun %s with a different locale selection.
 Enter it again:  Enter new superuser password:  If you want to create a new database system, either remove or empty
the directory "%s" or run %s
with an argument other than "%s".
 If you want to store the WAL there, either remove or empty the directory
"%s".
 It contains a dot-prefixed/invisible file, perhaps due to it being a mount point.
 It contains a lost+found directory, perhaps due to it being a mount point.
 Passwords didn't match.
 Please log in (using, e.g., "su") as the (unprivileged) user that will
own the server process.
 Rerun %s with the -E option.
 Running in debug mode.
 Running in no-clean mode.  Mistakes will not be cleaned up.
 The database cluster will be initialized with locale "%s".
 The database cluster will be initialized with locales
  COLLATE:  %s
  CTYPE:    %s
  MESSAGES: %s
  MONETARY: %s
  NUMERIC:  %s
  TIME:     %s
 The default database encoding has accordingly been set to "%s".
 The default text search configuration will be set to "%s".
 The encoding you selected (%s) and the encoding that the
selected locale uses (%s) do not match.  This would lead to
misbehavior in various character string processing functions.
Rerun %s and either do not specify an encoding explicitly,
or choose a matching combination.
 The files belonging to this database system will be owned by user "%s".
This user must also own the server process.

 The program "%s" is needed by %s but was not found in the
same directory as "%s".
Check your installation. The program "%s" was found by "%s"
but was not the same version as %s.
Check your installation. This might mean you have a corrupted installation or identified
the wrong directory with the invocation option -L.
 Try "%s --help" for more information.
 Usage:
 Using a mount point directly as the data directory is not recommended.
Create a subdirectory under the mount point.
 WAL directory "%s" not removed at user's request WAL directory location must be an absolute path You can change this by editing pg_hba.conf or using the option -A, or
--auth-local and --auth-host, the next time you run initdb.
 You must identify the directory where the data for this database system
will reside.  Do this with either the invocation option -D or the
environment variable PGDATA.
 argument of --wal-segsize must be a number argument of --wal-segsize must be a power of 2 between 1 and 1024 cannot be run as root cannot create restricted tokens on this platform: error code %lu cannot duplicate null pointer (internal error)
 caught signal
 child process exited with exit code %d child process exited with unrecognized status %d child process was terminated by exception 0x%X child process was terminated by signal %d: %s command not executable command not found could not access directory "%s": %m could not access file "%s": %m could not allocate SIDs: error code %lu could not change directory to "%s": %m could not change permissions of "%s": %m could not change permissions of directory "%s": %m could not close directory "%s": %m could not create directory "%s": %m could not create restricted token: error code %lu could not create symbolic link "%s": %m could not execute command "%s": %m could not find a "%s" to execute could not find suitable encoding for locale "%s" could not find suitable text search configuration for locale "%s" could not fsync file "%s": %m could not get exit code from subprocess: error code %lu could not get junction for "%s": %s
 could not identify current directory: %m could not load library "%s": error code %lu could not look up effective user ID %ld: %s could not open directory "%s": %m could not open file "%s" for reading: %m could not open file "%s" for writing: %m could not open file "%s": %m could not open process token: error code %lu could not re-execute with restricted token: error code %lu could not read binary "%s" could not read directory "%s": %m could not read password from file "%s": %m could not read symbolic link "%s": %m could not remove file or directory "%s": %m could not rename file "%s" to "%s": %m could not set environment could not set junction for "%s": %s
 could not start process for command "%s": error code %lu could not stat file "%s": %m could not stat file or directory "%s": %m could not write file "%s": %m could not write to child process: %s
 creating configuration files ...  creating directory %s ...  creating subdirectories ...  data directory "%s" not removed at user's request directory "%s" exists but is not empty enabling "trust" authentication for local connections encoding mismatch error:  failed to remove WAL directory failed to remove contents of WAL directory failed to remove contents of data directory failed to remove data directory failed to restore old locale "%s" fatal:  file "%s" does not exist file "%s" is not a regular file fixing permissions on existing directory %s ...  input file "%s" does not belong to PostgreSQL %s input file location must be an absolute path invalid authentication method "%s" for "%s" connections invalid binary "%s" invalid locale name "%s" invalid locale settings; check LANG and LC_* environment variables locale "%s" requires unsupported encoding "%s" logfile must specify a password for the superuser to enable password authentication no data directory specified ok
 out of memory out of memory
 password file "%s" is empty password prompt and password file cannot be specified together performing post-bootstrap initialization ...  removing WAL directory "%s" removing contents of WAL directory "%s" removing contents of data directory "%s" removing data directory "%s" running bootstrap script ...  selecting default max_connections ...  selecting default shared_buffers ...  selecting default time zone ...  selecting dynamic shared memory implementation ...  setlocale() failed specified text search configuration "%s" might not match locale "%s" suitable text search configuration for locale "%s" is unknown superuser name "%s" is disallowed; role names cannot begin with "pg_" symlinks are not supported on this platform syncing data to disk ...  too many command-line arguments (first is "%s") user does not exist user name lookup failure: error code %lu warning:  Project-Id-Version: initdb (PostgreSQL) 14
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2025-02-16 20:37+0000
PO-Revision-Date: 2021-10-13 23:44-0500
Last-Translator: Carlos Chapi <carloswaldo@babelruins.org>
Language-Team: PgSQL-es-Ayuda <pgsql-es-ayuda@lists.postgresql.org>
Language: es
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
X-Generator: BlackCAT 1.1
 
Si el directorio de datos no es especificado, se usa la variable de
ambiente PGDATA.
 
Opciones menos usadas:
 
Opciones:
 
Otras opciones:
 
Reporte errores a <%s>.
 
Completado. Ahora puede iniciar el servidor de bases de datos usando:

    %s

 
La sincronización a disco se ha omitido.
El directorio de datos podría corromperse si el sistema operativo sufre
una caída.
   --auth-host=MÉTODO        método de autentificación por omisión para
                            conexiones locales TCP/IP
   --auth-local=MÉTODO       método de autentificación por omisión para
                            conexiones de socket local
       --discard-caches      establece debug_discard_caches=1
       --lc-collate=, --lc-ctype=, --lc-messages=LOCALE
      --lc-monetary=, --lc-numeric=, --lc-time=LOCALE
                            inicializar usando esta configuración regional
                            en la categoría respectiva (el valor por omisión
                            es tomado de variables de ambiente)
       --locale=LOCALE       configuración regional por omisión para 
                            nuevas bases de datos
       --no-instructions     no mostrar instrucciones para los siguientes pasos
       --no-locale           equivalente a --locale=C
       --pwfile=ARCHIVO      leer contraseña del nuevo superusuario del archivo
       --wal-segsize=TAMAÑO  tamaño de los segmentos de WAL, en megabytes
   %s [OPCIÓN]... [DATADIR]
   -?, --help                mostrar esta ayuda y salir
   -A, --auth=MÉTODO         método de autentificación por omisión para
                            conexiones locales
   -E, --encoding=CODIF      codificación por omisión para nuevas bases de datos
   -L DIRECTORIO             donde encontrar los archivos de entrada
   -N, --no-sync             no esperar que los cambios se sincronicen a disco
   -S, --sync-only           sólo sincronizar el directorio de datos
   -T, --text-search-config=CONF
                            configuración de búsqueda en texto por omisión
   -U, --username=USUARIO    nombre del superusuario del cluster
   -V, --version             mostrar información de version y salir
   -W, --pwprompt            pedir una contraseña para el nuevo superusuario
   -X, --waldir=WALDIR       ubicación del directorio WAL
   -d, --debug               genera mucha salida de depuración
   -g, --allow-group-access  dar al grupo permisos de lectura/ejecución sobre
                            el directorio de datos
   -k, --data-checksums      activar sumas de verificación en páginas de datos
   -n, --no-clean            no limpiar después de errores
   -s, --show                muestra variables internas
  [-D, --pgdata=]DATADIR     ubicación para este cluster de bases de datos
 «%s» no es un nombre válido de codificación Sitio web de %s: <%s>
 %s inicializa un cluster de base de datos PostgreSQL.

 %s() falló: %m Verifique su instalación o especifique la ruta correcta usando la opción -L.
 Las sumas de verificación en páginas de datos han sido desactivadas.
 Las sumas de verificación en páginas de datos han sido activadas.
 La codificación «%s», implícita en la configuración regional,
no puede ser usada como codificación del lado del servidor.
La codificación por omisión será «%s».
 La codificación «%s» no puede ser usada como codificación del lado
del servidor.
Ejecute %s nuevamente con una selección de configuración regional diferente.
 Ingrésela nuevamente:  Ingrese la nueva contraseña del superusuario:  Si quiere crear un nuevo cluster de bases de datos, elimine o vacíe
el directorio «%s», o ejecute %s
con un argumento distinto de «%s».
 Si quiere almacenar el WAL ahí, elimine o vacíe el directorio
«%s».
 Contiene un archivo invisible, quizás por ser un punto de montaje.
 Contiene un directorio lost+found, quizás por ser un punto de montaje.
 Las contraseñas no coinciden.
 Por favor conéctese (usando, por ejemplo, «su») con un usuario no privilegiado,
quien ejecutará el proceso servidor.
 Ejecute %s con la opción -E.
 Ejecutando en modo de depuración.
 Ejecutando en modo no-clean.  Los errores no serán limpiados.
 El cluster será inicializado con configuración regional «%s».
 El cluster será inicializado con las configuraciones regionales
  COLLATE:  %s
  CTYPE:    %s
  MESSAGES: %s
  MONETARY: %s
  NUMERIC:  %s
  TIME:     %s
 La codificación por omisión ha sido por lo tanto definida a «%s».
 La configuración de búsqueda en texto ha sido definida a «%s».
 La codificación que seleccionó (%s) y la codificación de la configuración
regional elegida (%s) no coinciden.  Esto llevaría a comportamientos
erráticos en ciertas funciones de procesamiento de cadenas de caracteres.
Ejecute %s nuevamente y no especifique una codificación, o bien especifique
una combinación adecuada.
 Los archivos de este cluster serán de propiedad del usuario «%s».
Este usuario también debe ser quien ejecute el proceso servidor.

 %s necesita el programa «%s», pero no pudo encontrarlo en el mismo
directorio que «%s».
Verifique su instalación. El programa «%s» fue encontrado por «%s»,
pero no es de la misma versión que %s.
Verifique su instalación. Esto puede significar que tiene una instalación corrupta o ha
identificado el directorio equivocado con la opción -L.
 Use «%s --help» para obtener mayor información.
 Empleo:
 Usar un punto de montaje directamente como directorio de datos no es
recomendado.  Cree un subdirectorio bajo el punto de montaje.
 directorio de WAL «%s» no eliminado a petición del usuario la ubicación del directorio de WAL debe ser una ruta absoluta Puede cambiar esto editando pg_hba.conf o usando el parámetro -A,
o --auth-local y --auth-host la próxima vez que ejecute initdb.
 Debe especificar el directorio donde residirán los datos para este clúster.
Hágalo usando la opción -D o la variable de ambiente PGDATA.
 el argumento de --wal-segsize debe ser un número el argumento de --wal-segsize debe ser una potencia de 2 entre 1 y 1024 no se puede ejecutar como «root» no se pueden crear tokens restrigidos en esta plataforma: código de error %lu no se puede duplicar un puntero nulo (error interno)
 se ha capturado una señal
 el proceso hijo terminó con código de salida %d el proceso hijo terminó con código no reconocido %d el proceso hijo fue terminado por una excepción 0x%X el proceso hijo fue terminado por una señal %d: %s la orden no es ejecutable orden no encontrada no se pudo acceder al directorio «%s»: %m no se pudo acceder al archivo «%s»: %m no se pudo emplazar los SIDs: código de error %lu no se pudo cambiar al directorio «%s»: %m no se pudo cambiar los permisos de «%s»: %m no se pudo cambiar los permisos del directorio «%s»: %m no se pudo abrir el directorio «%s»: %m no se pudo crear el directorio «%s»: %m no se pudo crear el token restringido: código de error %lu no se pudo crear el enlace simbólico «%s»: %m no se pudo ejecutar la orden «%s»: %m no se pudo encontrar un «%s» para ejecutar no se pudo encontrar una codificación apropiada para
la configuración regional «%s» no se pudo encontrar una configuración para búsqueda en texto apropiada
para la configuración regional «%s» no se pudo sincronizar (fsync) archivo «%s»: %m no se pudo obtener el código de salida del subproceso»: código de error %lu no se pudo obtener junction para «%s»: %s
 no se pudo identificar el directorio actual: %m no se pudo cargar la biblioteca «%s»: código de error %lu no se pudo buscar el ID de usuario efectivo %ld: %s no se pudo abrir el directorio «%s»: %m no se pudo abrir archivo «%s» para lectura: %m no se pudo abrir el archivo «%s» para escritura: %m no se pudo abrir el archivo «%s»: %m no se pudo abrir el token de proceso: código de error %lu no se pudo re-ejecutar con el token restringido: código de error %lu no se pudo leer el binario «%s» no se pudo leer el directorio «%s»: %m no se pudo leer la contraseña desde el archivo «%s»: %m no se pudo leer el enlace simbólico «%s»: %m no se pudo borrar el archivo o el directorio «%s»: %m no se pudo renombrar el archivo de «%s» a «%s»: %m no se pudo establecer el ambiente no se pudo definir un junction para «%s»: %s
 no se pudo iniciar el proceso para la orden «%s»: código de error %lu no se pudo hacer stat al archivo «%s»: %m no se pudo hacer stat al archivo o directorio «%s»: %m no se pudo escribir el archivo «%s»: %m no se pudo escribir al proceso hijo: %s
 creando archivos de configuración ...  creando el directorio %s ...  creando subdirectorios ...  directorio de datos «%s» no eliminado a petición del usuario el directorio «%s» existe pero no está vacío activando el método de autentificación «trust» para conexiones locales codificaciones no coinciden error:  no se pudo eliminar el directorio de WAL no se pudo eliminar el contenido del directorio de WAL no se pudo eliminar el contenido del directorio de datos no se pudo eliminar el directorio de datos no se pudo restaurar la configuración regional anterior «%s» fatal:  el archivo «%s» no existe el archivo «%s» no es un archivo regular corrigiendo permisos en el directorio existente %s ...  el archivo de entrada «%s» no pertenece a PostgreSQL %s la ubicación de archivos de entrada debe ser una ruta absoluta método de autentificación «%s» no válido para conexiones «%s» el binario «%s» no es válido nombre de configuración regional «%s» no es válido configuración regional inválida; revise las variables de entorno LANG y LC_* la configuración regional «%s» requiere la codificación no soportada «%s» archivo_de_registro debe especificar una contraseña al superusuario para activar autentificación mediante contraseña no se especificó un directorio de datos hecho
 memoria agotada memoria agotada
 el archivo de contraseña «%s» está vacío la petición de contraseña y el archivo de contraseña no pueden
ser especificados simultáneamente realizando inicialización post-bootstrap ...  eliminando el directorio de WAL «%s» eliminando el contenido del directorio de WAL «%s» eliminando el contenido del directorio «%s» eliminando el directorio de datos «%s» ejecutando script de inicio (bootstrap) ...  seleccionando el valor para max_connections ...  seleccionando el valor para shared_buffers ...  seleccionando el huso horario por omisión ...  seleccionando implementación de memoria compartida dinámica ...  setlocale() falló la configuración de búsqueda en texto «%s» especificada podría no coincidir con la configuración regional «%s» la configuración de búsqueda en texto apropiada para la configuración regional «%s» es desconocida nombre de superusuario «%s» no permitido; los nombres de rol no pueden comenzar con «pg_» los enlaces simbólicos no están soportados en esta plataforma sincronizando los datos a disco ...  demasiados argumentos en la línea de órdenes (el primero es «%s») el usuario no existe fallo en la búsqueda de nombre de usuario: código de error %lu precaución:  