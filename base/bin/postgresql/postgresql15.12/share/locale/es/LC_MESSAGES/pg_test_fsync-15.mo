��    $      <  5   \      0  1   1  2   c  /   �     �  8   �          4     O     j     �     �     �  (   �  U   �  [   F  K   �  c   �     R  .   m  F   �  E   �  %   )  +   O  !   {     �     �     �     �     �     �     �        /     	   5     ?  �  L  P   /
  Q   �
  :   �
  &     P   4     �     �     �     �                  A  .   H  e   w  o   �  d   M  l   �       2   >  K   q  J   �  -     ,   6  )   c  %   �  	   �     �     �  	   �  '   �            E        S     a     "          
                            !                                  $                  	                   #                                                                  
Compare file sync methods using one %dkB write:
 
Compare file sync methods using two %dkB writes:
 
Compare open_sync with different write sizes:
 
Non-sync'ed %dkB writes:
 
Test if fsync on non-write file descriptor is honored:
  1 * 16kB open_sync write  2 *  8kB open_sync writes  4 *  4kB open_sync writes  8 *  2kB open_sync writes %13.3f ops/sec  %6.0f usecs/op
 %s must be in range %u..%u %s: %m %u second per test
 %u seconds per test
 (If the times are similar, fsync() can sync data written on a different
descriptor.)
 (This is designed to compare the cost of writing 16kB in different write
open_sync sizes.)
 (in wal_sync_method preference order, except fdatasync is Linux's default)
 * This file system and its mount options do not support direct
  I/O, e.g. ext4 in journaled mode.
 16 *  1kB open_sync writes Direct I/O is not supported on this platform.
 F_NOCACHE supported on this platform for open_datasync and open_sync.
 O_DIRECT supported on this platform for open_datasync and open_sync.
 Try "%s --help" for more information. Usage: %s [-f FILENAME] [-s SECS-PER-TEST]
 could not create thread for alarm could not open output file detail:  error:  fsync failed hint:  invalid argument for option %s n/a n/a* too many command-line arguments (first is "%s") warning:  write failed Project-Id-Version: pg_test_fsync (PostgreSQL) 15
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2025-02-16 20:25+0000
PO-Revision-Date: 2022-10-20 09:06+0200
Last-Translator: Carlos Chapi <carloswaldo@babelruins.org>
Language-Team: PgSQL-es-Ayuda <pgsql-es-ayuda@lists.postgresql.org>
Language: es
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=2; plural=n != 1;
X-Generator: Poedit 2.4.3
 
Comparar métodos de sincronización de archivos usando una escritura de %dkB:
 
Comparar métodos de sincronización de archivos usando dos escrituras de %dkB:
 
Comparar open_sync con diferentes tamaños de escritura:
 
Escrituras de %dkB no sincronizadas:
 
Probar si se respeta fsync en un descriptor de archivo que no es de escritura:
  1 * 16kB escritura open_sync  2 *  8kB escrituras open_sync  4 *  4kB escrituras open_sync  8 *  2kB escrituras open_sync %13.3f ops/seg  %6.0f usegs/op
 %s debe estar en el rango %u..%u %s: %m %u segundo por prueba
 %u segundos por prueba
 (Si los tiempos son similares, fsync() puede sincronizar datos escritos
en un descriptor diferente.)
 (Esto está diseñado para comparar el costo de escribir 16kB en diferentes
tamaños de escrituras open_sync.)
 (en orden de preferencia de wal_sync_method, excepto en Linux donde fdatasync es el predeterminado)
 * Este sistema de archivos con sus opciones de montaje no soportan
  Direct I/O, e.g. ext4 en modo journal.
 16 *  1kB escrituras open_sync Direct I/O no está soportado en esta plataforma.
 F_NOCACHE tiene soporte en esta plataforma para open_datasync y open_sync.
 O_DIRECT tiene soporte en esta plataforma para open_datasync y open_sync.
 Pruebe «%s --help» para mayor información. Empleo: %s [-f ARCHIVO] [-s SEG-POR-PRUEBA]
 no se pudo crear el thread para la alarma no se pudo abrir el archivo de salida detalle:  error:  fsync falló consejo:  argumento no válido para la opción %s n/a n/a* demasiados argumentos en la línea de órdenes (el primero es «%s») precaución:  escritura falló 