��    ]           �      �  
   �     �  %     3   1  P   e  �   �  P   @	  ?   �	  I   �	  =   
  A   Y
  6   �
  �   �
  D   �  �   �  >   �  �   �  B   b  C   �  ~   �  D   h     �  9   �  4   �  2   0  ;   c  @   �  R   �     3  :   S  %   �     �  �   �  P   C  Q   �  �   �      �  -   �  )        <     X     u  6   �  !   �     �          #  '   @  *   h  5   �  T   �  I     @   h  =   �  +   �       .         K     l  |   t     �  ;   �     4     O     j  5   �     �  3   �  6   	  1   @     r  $   �  '   �  $   �  *         .  \   O     �     �  ,   �  6     !   C  F   e  .   �  #   �  $   �  0   $  $   U  /   z  6   �  $   �  	     �       �     �  *     ;   C  x     �   �  \   �  I   �  T   B   M   �   U   �   :   ;!    v!  H   x"  �   �"  H   g#  �   �#  V   D$  Q   �$  �   �$  J   �%     �%  N   �%  <   5&  H   r&  K   �&  W   '  g   _'  )   �'  M   �'  -   ?(     m(  �   v(  f   )  n   k)    �)     �*  2   +  :   :+  (   u+  *   �+  '   �+  G   �+  )   9,  "   c,  &   �,  %   �,  3   �,  >   -  >   F-  d   �-  N   �-  J   9.  E   �.  4   �.  	   �.  =   	/  %   G/     m/  �   u/  	   0  \   &0  $   �0  $   �0     �0  =   �0  %   +1  6   Q1  ;   �1  C   �1  )   2  /   22  /   b2  .   �2  8   �2  4   �2  n   /3  #   �3  *   �3  7   �3  J   %4  #   p4  Q   �4  0   �4  &   5  &   >5  ?   e5  %   �5  E   �5  5   6  *   G6     r6        @           O      M       L   =           (   -            .   U                 ;   Z   B      Q   S   G   V      H   T          K   0                    <       4   D   A       #           ]   ,   "   X       R       E   Y          2   W   F         +   /      J          *   :   C   '       	       5      $       %       9       6           1       7   P   8         I   &          >   
      [           )   ?   !                        N       3                 \    
Options:
 
Report bugs to <%s>.
   %s [OPTION]... [STARTSEG [ENDSEG]]
   -?, --help             show this help, then exit
   -B, --block=N          with --relation, only show records that modify block N
   -F, --fork=FORK        only show records that modify blocks in fork FORK;
                         valid names are main, fsm, vm, init
   -R, --relation=T/D/R   only show records that modify blocks in relation T/D/R
   -V, --version          output version information, then exit
   -b, --bkp-details      output detailed information about backup blocks
   -e, --end=RECPTR       stop reading at WAL location RECPTR
   -f, --follow           keep retrying after reaching end of WAL
   -n, --limit=N          number of records to display
   -p, --path=PATH        directory in which to find log segment files or a
                         directory with a ./pg_wal that contains such files
                         (default: current directory, ./pg_wal, $PGDATA/pg_wal)
   -q, --quiet            do not print any output, except for errors
   -r, --rmgr=RMGR        only show records generated by resource manager RMGR;
                         use --rmgr=list to list valid resource manager names
   -s, --start=RECPTR     start reading at WAL location RECPTR
   -t, --timeline=TLI     timeline from which to read log records
                         (default: 1 or the value used in STARTSEG)
   -w, --fullpage         only show records with a full page write
   -x, --xid=XID          only show records with transaction ID XID
   -z, --stats[=record]   show statistics instead of records
                         (optionally, show per-record statistics)
 %s decodes and displays PostgreSQL write-ahead logs for debugging.

 %s home page: <%s>
 BKPBLOCK_HAS_DATA not set, but data length is %u at %X/%X BKPBLOCK_HAS_DATA set, but no data included at %X/%X BKPBLOCK_SAME_REL set but no previous rel at %X/%X BKPIMAGE_COMPRESSED set, but block image length %u at %X/%X BKPIMAGE_HAS_HOLE not set, but hole offset %u length %u at %X/%X BKPIMAGE_HAS_HOLE set, but hole offset %u length %u block image length %u at %X/%X ENDSEG %s is before STARTSEG %s Expecting "tablespace OID/database OID/relation filenode". Try "%s --help" for more information. Usage:
 WAL file is from different database system: WAL file database system identifier is %llu, pg_control database system identifier is %llu WAL file is from different database system: incorrect XLOG_BLCKSZ in page header WAL file is from different database system: incorrect segment size in page header WAL segment size must be a power of two between 1 MB and 1 GB, but the WAL file "%s" header specifies %d byte WAL segment size must be a power of two between 1 MB and 1 GB, but the WAL file "%s" header specifies %d bytes contrecord is requested by %X/%X could not decompress image at %X/%X, block %d could not find a valid record after %X/%X could not find any WAL file could not find file "%s": %m could not locate WAL file "%s" could not locate backup block with ID %d in WAL record could not open directory "%s": %m could not open file "%s" could not open file "%s": %m could not read file "%s": %m could not read file "%s": read %d of %d could not read from file %s, offset %d: %m could not read from file %s, offset %d: read %d of %d could not restore image at %X/%X compressed with %s not supported by build, block %d could not restore image at %X/%X compressed with unknown method, block %d could not restore image at %X/%X with invalid block %d specified could not restore image at %X/%X with invalid state, block %d custom resource manager "%s" does not exist detail:  end WAL location %X/%X is not inside file "%s" error in WAL record at %X/%X: %s error:  first record is after %X/%X, at %X/%X, skipping over %u byte
 first record is after %X/%X, at %X/%X, skipping over %u bytes
 hint:  incorrect resource manager data checksum in record at %X/%X invalid WAL location: "%s" invalid block number: "%s" invalid block_id %u at %X/%X invalid contrecord length %u (expected %lld) at %X/%X invalid fork name: "%s" invalid info bits %04X in log segment %s, offset %u invalid magic number %04X in log segment %s, offset %u invalid record length at %X/%X: wanted %u, got %u invalid record offset at %X/%X invalid relation specification: "%s" invalid resource manager ID %u at %X/%X invalid timeline specification: "%s" invalid transaction ID specification: "%s" invalid value "%s" for option %s neither BKPIMAGE_HAS_HOLE nor BKPIMAGE_COMPRESSED set, but block image length is %u at %X/%X no arguments specified no start WAL location given option %s requires option %s to be specified out of memory while allocating a WAL reading processor out-of-order block_id %u at %X/%X out-of-sequence timeline ID %u (after %u) in log segment %s, offset %u record with incorrect prev-link %X/%X at %X/%X record with invalid length at %X/%X resource manager "%s" does not exist start WAL location %X/%X is not inside file "%s" there is no contrecord flag at %X/%X too many command-line arguments (first is "%s") unexpected pageaddr %X/%X in log segment %s, offset %u unrecognized value for option %s: %s warning:  Project-Id-Version: pg_waldump (PostgreSQL) 15
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2025-02-16 20:20+0000
PO-Revision-Date: 2022-11-04 13:17+0100
Last-Translator: Carlos Chapi <carlos.chapi@2ndquadrant.com>
Language-Team: PgSQL-es-Ayuda <pgsql-es-ayuda@lists.postgresql.org>
Language: es
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
X-Generator: Poedit 2.0.2
Plural-Forms: nplurals=2; plural=n != 1;
 
Opciones:
 
Reporte errores a <%s>.
   %s [OPCIÓN]... [SEGINICIAL [SEGFINAL]]
   -?, --help               mostrar esta ayuda, luego salir
   -B, --block=N            con --relation, sólo mostrar registros que modifican
                           el bloque N
   -F, --form=FORK          sólo mostrar registros que modifican bloques en el
                           «fork» FORK; nombres válidos son main, fsm, vm, init
   -R, --relation=T/D/R     sólo mostrar registros que modifican bloques en relación T/D/R
   -V, --version            mostrar información de versión, luego salir
   -b, --bkp-details        mostrar información detallada sobre bloques de respaldo
   -e, --end=RECPTR         detener la lectura del WAL en la posición RECPTR
   -f, --follow             seguir reintentando después de alcanzar el final del WAL
   -n, --limit=N            número de registros a mostrar
   -p, --path=RUTA          directorio donde buscar los archivos de segmento de WAL
                           o un directorio con un ./pg_wal que contenga tales archivos
                           (por omisión: directorio actual, ./pg_wal, $PGDATA/pg_wal)
   -q, --quiet              no escribir ningún mensaje, excepto errores
   -r, --rmgr=GREC          sólo mostrar registros generados por el gestor de
                           recursos GREC; use --rmgr=list para listar nombres válidos
   -s, --start=RECPTR       empezar a leer el WAL en la posición RECPTR
   -t, --timeline=TLI       timeline del cual leer los registros de WAL
                           (por omisión: 1 o el valor usado en SEGINICIAL)
   -w, --fullpage           sólo mostrar registros con escrituras de página completa
   -x, --xid=XID            sólo mostrar registros con el id de transacción XID
   -z, --stats[=registro]   mostrar estadísticas en lugar de registros
                           (opcionalmente, mostrar estadísticas por registro)
 %s decodifica y muestra segmentos de WAL de PostgreSQL para depuración.

 Sitio web de %s: <%s>
 BKPBLOCK_HAS_DATA no está definido, pero el largo de los datos es %u en %X/%X BKPBLOCK_HAS_DATA está definido, pero no hay datos en %X/%X BKPBLOCK_SAME_REL está definido, pero no hay «rel» anterior en %X/%X  BKPIMAGE_COMPRESSED definido, pero largo de imagen de bloque es %u en %X/%X BKPIMAGE_HAS_HOLE no está definido, pero posición del agujero es %u largo %u en %X/%X BKPIMAGE_HAS_HOLE está definido, pero posición del agujero es %u largo %u largo de imagen %u en %X/%X SEGFINAL %s está antes del SEGINICIAL %s Se esperaba «OID de tablespace/OID de base de datos/filenode de relación». Pruebe «%s --help» para mayor información. Empleo:
 archivo WAL es de un sistema de bases de datos distinto: identificador de sistema en archivo WAL es %llu, identificador en pg_control es %llu archivo WAL es de un sistema de bases de datos distinto: XLOG_BLCKSZ incorrecto en cabecera de paǵina archivo WAL es de un sistema de bases de datos distinto: tamaño de segmento incorrecto en cabecera de paǵina el tamaño de segmento WAL debe ser una potencia de dos entre 1 MB y 1 GB, pero la cabecera del archivo WAL «%s» especifica %d byte el tamaño de segmento WAL debe ser una potencia de dos entre 1 MB y 1 GB, pero la cabecera del archivo WAL «%s» especifica %d bytes contrecord solicitado por %X/%X no se pudo descomprimir imagen en %X/%X, bloque %d no se pudo encontrar un registro válido después de %X/%X no se pudo encontrar ningún archivo WAL no se pudo encontrar el archivo «%s»: %m no se pudo ubicar el archivo WAL «%s» no se pudo localizar un bloque de respaldo con ID %d en el registro WAL no se pudo abrir el directorio «%s»: %m no se pudo abrir el archivo «%s» no se pudo abrir el archivo «%s»: %m no se pudo leer el archivo «%s»: %m no se pudo leer el archivo «%s»: leídos %d de %d no se pudo leer desde el archivo «%s» en la posición %d: %m no se pudo leer del archivo %s, posición %d: leídos %d de %d no se pudo restaurar imagen en %X/%X comprimida con %s no soportado por esta instalación, bloque %d no se pudo restaurar imagen en %X/%X comprimida método desconocido, bloque %d no se pudo restaurar imagen en %X/%X con bloque especificado %d no válido no se pudo restaurar imagen en %X/%X con estado no válido, bloque %d el gestor de recursos personalizado «%s» no existe detalle:  la posición final de WAL %X/%X no está en el archivo «%s» error en registro de WAL en %X/%X: %s error:  el primer registro está ubicado después de %X/%X, en %X/%X, saltándose %u byte
 el primer registro está ubicado después de %X/%X, en %X/%X, saltándose %u bytes
 consejo:  suma de verificación de los datos del gestor de recursos incorrecta en el registro en %X/%X ubicación de WAL no válida: «%s» número de bloque no válido: «%s» block_id %u no válido en %X/%X largo de contrecord %u no válido (se esperaba %lld) en %X/%X nombre de «fork» no válido: «%s» info bits %04X no válidos en archivo %s, posición %u número mágico %04X no válido en archivo %s, posición %u largo de registro no válido en %X/%X: se esperaba %u, se obtuvo %u posición de registro no válida en %X/%X especificación de relación no válida: «%s» ID de gestor de recursos %u no válido en %X/%X especificación de timeline no válida: «%s» especificación de ID de transacción no válida: «%s» el valor «%s» no es válido para la opción «%s» ni BKPIMAGE_HAS_HOLE ni BKPIMAGE_COMPRESSED están definidos, pero el largo de imagen de bloque es %u en %X/%X no se especificó ningún argumento no se especificó posición inicial de WAL la opción %s requiere que se especifique la opción %s se agotó la memoria mientras se emplazaba un procesador de lectura de WAL block_id %u fuera de orden en %X/%X ID de timeline %u fuera de secuencia (después de %u) en archivo %s, posición %u registro con prev-link %X/%X incorrecto en %X/%X registro con largo no válido en %X/%X el gestor de recursos «%s» no existe la posición inicial de WAL %X/%X no está en el archivo «%s» no hay bandera de contrecord en %X/%X demasiados argumentos en la línea de órdenes (el primero es «%s») pageaddr %X/%X inesperado en archivo %s, posición %u valor no reconocido para la opción %s: %s precaución:  