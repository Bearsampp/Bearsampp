��    w      �  �   �      
     
  9   3
     m
  F   �
  =   �
  D   	  I   N  �   �  A   W  ;   �  M   �  K   #  K   o  0   �  =   �  ;   *  2   f     �  +   �     �  )   �  )     )   =     g  )   �  )   �  +   �  )     R   .  )   �  )   �     �  U   �  A   H  )   �  )   �  )   �  ,     )   5  )   _  )   �  )   �  )   �  )     )   1  )   [  )   �  )   �  )   �  )     )   -  )   W  )   �  )   �  )   �  )   �     )  )   @  )   j  )   �  )   �  	   �  )   �  �     %   �  !   �  )        /  ,   F  *   s  A   �     �     �     �  @     '   T  &   |  "   �  1   �     �  7     +   O  !   {  (   �     �  ,   �  :     !   K     m  0   �  8   �     �  "        5     >     F     V     ]     |  &   �  +   �  )   �          +  -   /  >   ]  )   �     �  ;   �  =     �   C  )   �  /   
  B   :  7   }  (   �     �  	   �  �       �   C   �      C!  F   ]!  6   �!  n   �!  D   J"  �   �"  O   w#  B   �#  q   
$  �   |$  H   %  3   Z%  g   �%  b   �%  /   Y&     �&  ;   �&     �&  5   �&  4   %'  7   Z'  !   �'  6   �'  5   �'  1   !(  4   S(  `   �(  5   �(  5   )  !   U)  f   w)  S   �)  5   2*  5   h*  5   �*  8   �*  5   +  5   C+  5   y+  5   �+  5   �+  5   ,  5   Q,  5   �,  5   �,  7   �,  5   +-  5   a-  5   �-  )   �-  )   �-  )   !.  )   K.  )   u.     �.  )   �.  )   �.  )   /  )   7/  
   a/  5   l/  �   �/  -   [0  !   �0  5   �0  !   �0  3   1  1   71  G   i1     �1  	   �1  .   �1  N   �1  2   H2  +   {2  )   �2  ;   �2  '   3  N   53  <   �3  )   �3  0   �3  &   4  :   C4  E   ~4  (   �4  %   �4  9   5  H   M5  )   �5  3   �5  	   �5     �5     6  	   6  '   #6      K6  -   l6  5   �6  )   �6  #   �6     7  :   *7  C   e7  )   �7     �7  G   �7  N   $8  �   s8  6   89  E   o9  >   �9  6   �9  6   +:      b:     �:         >   9   E   #   o      T                  1          6           !               \            d       C   `       S   
   p   n   ^   4       F   c   h       U   N   r       (       '       V      t   ,       *      W   B   3   Z       a   u   Y      q   P   v           %          	   e   J      @       A       H          _   G   l           0   m   ?   =   .   $             Q   s                                      [   O       2      5   +       j   M   f       w   8                     X   k       ;   /      7           K       R   ]   D   I   -   &       )   <   "          :       L   i   g       b        

Values to be changed:

 
If these values seem acceptable, use -f to force reset.
 
Report bugs to <%s>.
       --wal-segsize=SIZE           size of WAL segments, in megabytes
   -?, --help                       show this help, then exit
   -O, --multixact-offset=OFFSET    set next multitransaction offset
   -V, --version                    output version information, then exit
   -c, --commit-timestamp-ids=XID,XID
                                   set oldest and newest transactions bearing
                                   commit timestamp (zero means no change)
   -e, --epoch=XIDEPOCH             set next transaction ID epoch
   -f, --force                      force update to be done
   -l, --next-wal-file=WALFILE      set minimum starting location for new WAL
   -m, --multixact-ids=MXID,MXID    set next and oldest multitransaction ID
   -n, --dry-run                    no update, just show what would be done
   -o, --next-oid=OID               set next OID
   -u, --oldest-transaction-id=XID  set oldest transaction ID
   -x, --next-transaction-id=XID    set next transaction ID
  [-D, --pgdata=]DATADIR            data directory
 %s home page: <%s>
 %s resets the PostgreSQL write-ahead log.

 64-bit integers Blocks per segment of large relation: %u
 Bytes per WAL segment:                %u
 Catalog version number:               %u
 Current pg_control values:

 Data page checksum version:           %u
 Database block size:                  %u
 Database system identifier:           %llu
 Date/time type storage:               %s
 File "%s" contains "%s", which is not compatible with this program's version "%s". First log segment after reset:        %s
 Float8 argument passing:              %s
 Guessed pg_control values:

 If you are sure the data directory path is correct, execute
  touch %s
and try again. Is a server running?  If not, delete the lock file and try again. Latest checkpoint's NextMultiOffset:  %u
 Latest checkpoint's NextMultiXactId:  %u
 Latest checkpoint's NextOID:          %u
 Latest checkpoint's NextXID:          %u:%u
 Latest checkpoint's TimeLineID:       %u
 Latest checkpoint's full_page_writes: %s
 Latest checkpoint's newestCommitTsXid:%u
 Latest checkpoint's oldestActiveXID:  %u
 Latest checkpoint's oldestCommitTsXid:%u
 Latest checkpoint's oldestMulti's DB: %u
 Latest checkpoint's oldestMultiXid:   %u
 Latest checkpoint's oldestXID's DB:   %u
 Latest checkpoint's oldestXID:        %u
 Maximum columns in an index:          %u
 Maximum data alignment:               %u
 Maximum length of identifiers:        %u
 Maximum size of a TOAST chunk:        %u
 NextMultiOffset:                      %u
 NextMultiXactId:                      %u
 NextOID:                              %u
 NextXID epoch:                        %u
 NextXID:                              %u
 OID (-o) must not be 0 OldestMulti's DB:                     %u
 OldestMultiXid:                       %u
 OldestXID's DB:                       %u
 OldestXID:                            %u
 Options:
 Size of a large-object chunk:         %u
 The database server was not shut down cleanly.
Resetting the write-ahead log might cause data to be lost.
If you want to proceed anyway, use -f to force reset.
 Try "%s --help" for more information. Usage:
  %s [OPTION]... DATADIR

 WAL block size:                       %u
 Write-ahead log reset
 You must run %s as the PostgreSQL superuser. argument of --wal-segsize must be a number argument of --wal-segsize must be a power of 2 between 1 and 1024 by reference by value cannot be executed by "root" cannot create restricted tokens on this platform: error code %lu could not allocate SIDs: error code %lu could not change directory to "%s": %m could not close directory "%s": %m could not create restricted token: error code %lu could not delete file "%s": %m could not get exit code from subprocess: error code %lu could not load library "%s": error code %lu could not open directory "%s": %m could not open file "%s" for reading: %m could not open file "%s": %m could not open process token: error code %lu could not re-execute with restricted token: error code %lu could not read directory "%s": %m could not read file "%s": %m could not read permissions of directory "%s": %m could not start process for command "%s": error code %lu could not write file "%s": %m data directory is of wrong version detail:  error:  fsync error: %m hint:  invalid argument for option %s lock file "%s" exists multitransaction ID (-m) must not be 0 multitransaction offset (-O) must not be -1 newestCommitTsXid:                    %u
 no data directory specified off oldest multitransaction ID (-m) must not be 0 oldest transaction ID (-u) must be greater than or equal to %u oldestCommitTsXid:                    %u
 on pg_control exists but has invalid CRC; proceed with caution pg_control exists but is broken or wrong version; ignoring it pg_control specifies invalid WAL segment size (%d byte); proceed with caution pg_control specifies invalid WAL segment size (%d bytes); proceed with caution pg_control version number:            %u
 too many command-line arguments (first is "%s") transaction ID (-c) must be either 0 or greater than or equal to 2 transaction ID (-x) must be greater than or equal to %u transaction ID epoch (-e) must not be -1 unexpected empty file "%s" warning:  Project-Id-Version: pg_resetwal (PostgreSQL) 15
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2025-02-16 20:22+0000
PO-Revision-Date: 2022-10-20 09:06+0200
Last-Translator: Carlos Chapi <carlos.chapi@2ndquadrant.com>
Language-Team: PgSQL-es-Ayuda <pgsql-es-ayuda@lists.postgresql.org>
Language: es
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=2; plural=(n != 1);
X-Generator: BlackCAT 1.1
 

Valores a cambiar:

 
Si estos valores parecen aceptables, use -f para forzar reinicio.
 
Reporte errores a <%s>.
       --wal-segsize=TAMAÑO tamaño de segmentos de WAL, en megabytes
   -?, --help               mostrar esta ayuda y salir
   -O, --multixact-offset=OFFSET
                           asigna la siguiente posición de multitransacción
   -V, --version            mostrar información de versión y salir
   -c, --commit-timestamp-ids=XID,XID
                           definir la más antigua y la más nueva transacciones
                           que llevan timestamp de commit (cero significa no
                           cambiar)
   -e, --epoch=XIDEPOCH     asigna el siguiente «epoch» de ID de transacción
   -f, --force              fuerza que la actualización sea hecha
   -l, --next-wal-file=ARCHIVOWAL
                           fuerza una ubicación inicial mínima para nuevo WAL
   -m, --multixact-ids=MXID,MXID
                           asigna el siguiente ID de multitransacción y
                           el más antiguo
   -n, --dry-run            no actualiza, sólo muestra lo que se haría
   -o, --next-oid=OID       asigna el siguiente OID
   -u, --oldest-transaction-id=XID
                           asigna el ID de transacción más antiguo
   -x, --next-transaction-id=XID
                           asigna el siguiente ID de transacción
  [-D, --pgdata=]DATADIR    directorio de datos
 Sitio web de %s: <%s>
 %s restablece el WAL («write-ahead log») de PostgreSQL.

 enteros de 64 bits Bloques por segmento de relación grande:         %u
 Bytes por segmento WAL:                          %u
 Número de versión de catálogo:                   %u
 Valores actuales de pg_control:

 Versión de suma de verificación de datos:        %u
 Tamaño del bloque de la base de datos:           %u
 Identificador de sistema:                   %llu
 Tipo de almacenamiento hora/fecha:               %s
 El archivo «%s» contiene «%s», que no es compatible con la versión «%s» de este programa. Primer segmento de log después de reiniciar:     %s
 Paso de parámetros float8:                       %s
 Valores de pg_control asumidos:

 Si está seguro que la ruta al directorio de datos es correcta, ejecute
   touch %s
y pruebe de nuevo. ¿Hay un servidor corriendo? Si no, borre el archivo candado e inténtelo de nuevo. NextMultiOffset del checkpoint más reciente:     %u
 NextMultiXactId del checkpoint más reciente:     %u
 NextOID del checkpoint más reciente:             %u
 NextXID del checkpoint más reciente:             %u:%u
 TimeLineID del checkpoint más reciente:          %u
 full_page_writes del checkpoint más reciente:    %s
 newestCommitTsXid del último checkpoint:         %u
 oldestActiveXID del checkpoint más reciente:     %u
 oldestCommitTsXid del último checkpoint:         %u
 BD del oldestMultiXid del checkpt. más reciente: %u
 oldestMultiXid del checkpoint más reciente:      %u
 BD del oldestXID del checkpoint más reciente:    %u
 oldestXID del checkpoint más reciente:           %u
 Máximo número de columnas en un índice:          %u
 Máximo alineamiento de datos:                    %u
 Longitud máxima de identificadores:              %u
 Longitud máxima de un trozo TOAST:               %u
 NextMultiOffset:                      %u
 NextMultiXactId:                      %u
 NextOID:                              %u
 Epoch del NextXID:                    %u
 NextXID:                              %u
 OID (-o) no debe ser cero Base de datos del OldestMulti:        %u
 OldestMultiXid:                       %u
 Base de datos del OldestXID:          %u
 OldestXID:                            %u
 Opciones:
 Longitud máxima de un trozo de objeto grande:    %u
 El servidor de bases de datos no se apagó limpiamente.
Restablecer el WAL puede causar pérdida de datos.
Si quiere continuar de todas formas, use -f para forzar el restablecimiento.
 Pruebe «%s --help» para mayor información. Uso:
   %s [OPCIÓN]... DATADIR

 Tamaño del bloque de WAL:                        %u
 «Write-ahead log» restablecido
 Debe ejecutar %s con el superusuario de PostgreSQL. el argumento de --wal-segsize debe ser un número el argumento de --wal-segsize debe ser una potencia de 2 entre 1 y 1024 por referencia por valor no puede ser ejecutado con el usuario «root» no se pueden crear tokens restrigidos en esta plataforma: código de error %lu no se pudo emplazar los SIDs: código de error %lu no se pudo cambiar al directorio «%s»: %m no se pudo abrir el directorio «%s»: %m no se pudo crear el token restringido: código de error %lu no se pudo borrar el archivo «%s»: %m no se pudo obtener el código de salida del subproceso»: código de error %lu no se pudo cargar la biblioteca «%s»: código de error %lu no se pudo abrir el directorio «%s»: %m no se pudo abrir archivo «%s» para lectura: %m no se pudo abrir el archivo «%s»: %m no se pudo abrir el token de proceso: código de error %lu no se pudo re-ejecutar con el token restringido: código de error %lu no se pudo leer el directorio «%s»: %m no se pudo leer el archivo «%s»: %m no se pudo obtener los permisos del directorio «%s»: %m no se pudo iniciar el proceso para la orden «%s»: código de error %lu no se pudo escribir el archivo «%s»: %m el directorio de datos tiene la versión equivocada detalle:  error:  error de fsync: %m consejo:  argumento no válido para la opción %s el archivo candado «%s» existe el ID de multitransacción (-m) no debe ser 0 la posición de multitransacción (-O) no debe ser -1 newestCommitTsXid:                    %u
 directorio de datos no especificado desactivado el ID de multitransacción más antiguo (-m) no debe ser 0 el ID de transacción más antiguo (-u) debe ser mayor o igual a %u oldestCommitTsXid:                    %u
 activado existe pg_control pero tiene un CRC no válido, proceda con precaución existe pg_control pero está roto o tiene la versión equivocada; ignorándolo pg_control especifica un tamaño de segmento de WAL no válido (%d byte), proceda con precaución pg_control especifica un tamaño de segmento de WAL no válido (%d bytes), proceda con precaución Número de versión de pg_control:                 %u
 demasiados argumentos en la línea de órdenes (el primero es «%s») el ID de transacción (-c) debe ser 0 o bien mayor o igual a 2 el ID de transacción (-x) debe ser mayor o igual a %u el «epoch» de ID de transacción (-e) no debe ser -1 archivo vacío inesperado «%s» precaución:  