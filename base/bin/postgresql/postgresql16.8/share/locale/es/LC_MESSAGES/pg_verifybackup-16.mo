��    i      d  �   �       	     	  8   	  8   Q	  D   �	  8   �	  4   
  >   =
  <   |
  I   �
  9     ?   =  7   }     �  /   �  /     1   5     g     �  3   �  ,   �  !   �  $     $   =     b  $   �  .   �  &   �  '   �      #  	   D  $   N     s  %   �  I   �  d   �  8   _  3   �  #   �  "   �  #        7  $   U  /   z     �     �     �  "        &     D  (   _  '   �  *   �  )   �  !        '  #   D     h     �     �  )   �     �  )   �  &   (  %   O     u  ,   ~     �     �     �  4   �  6        T     p  $   w     �      �     �     �          -     =     O     \     n          �     �     �  L   �  A        X  /   s     �     �     �     �                2     J     b  %   t     �  	   �  �  �     �  >   �  ?     R   A  D   �  7   �  K     @   ]  K   �  R   �  R   =  K   �  "   �  8   �  8   8  :   q     �     �  [   �  4   <  ,   q  /   �  /   �  '   �  /   &  >   V  6   �  4   �  ,      
   .   .   9   !   h   -   �   T   �   �   !  O   �!  A   �!  "   !"  /   D"  &   t"  ,   �"  K   �"  5   #  '   J#  9   r#  #   �#  )   �#  '   �#  ,   "$  D   O$  <   �$  F   �$  >   %  )   W%  &   �%  >   �%  )   �%  '   &  %   9&  5   _&  +   �&  8   �&  E   �&  =   @'  	   ~'  H   �'     �'     �'  !   �'  S   (  B   k(  .   �(  	   �(  ;   �(  4   #)  )   X)  7   �)  &   �)  ,   �)     *     '*     @*     Q*     h*  -   z*     �*     �*     �*  b   �*  T   A+  #   �+  E   �+       ,     !,     9,     T,  !   p,     �,     �,     �,     �,  6   �,  %   ,-     R-     0             U   &       1       :   (   V          T       S           5   6   +      W          *      7                            X       '   =      R                        /              Z   @   ,   ?              ]   `   Y   4           -       C          O      P       I       A   [               >   M       E   F   d   "       b   G          9      g      L   e   
   !          J   c   D   \          B   .   2       Q   #          ;   f   H   )           _   a       	           $   <   %   ^             N   i      K   h             8           3    
Report bugs to <%s>.
   -?, --help                  show this help, then exit
   -P, --progress              show progress information
   -V, --version               output version information, then exit
   -e, --exit-on-error         exit immediately on error
   -i, --ignore=RELATIVE_PATH  ignore indicated path
   -m, --manifest-path=PATH    use specified path for manifest
   -n, --no-parse-wal          do not try to parse WAL files
   -q, --quiet                 do not print any output, except for errors
   -s, --skip-checksums        skip checksum verification
   -w, --wal-directory=PATH    use specified path for WAL files
 "%s" has size %lld on disk but size %zu in the manifest "%s" is not a file or directory "%s" is present in the manifest but not on disk "%s" is present on disk but not in the manifest "\u" must be followed by four hexadecimal digits. %*s/%s kB (%d%%) verified %s home page: <%s>
 %s verifies a backup against the backup manifest.

 Character with value 0x%02x must be escaped. Escape sequence "\%s" is invalid. Expected "," or "]", but found "%s". Expected "," or "}", but found "%s". Expected ":", but found "%s". Expected JSON value, but found "%s". Expected array element or "]", but found "%s". Expected end of input, but found "%s". Expected string or "}", but found "%s". Expected string, but found "%s". Options:
 The input string ended unexpectedly. Token "%s" is invalid. Try "%s --help" for more information. Unicode escape value could not be translated to the server's encoding %s. Unicode escape values cannot be used for code point values above 007F when the encoding is not UTF8. Unicode high surrogate must not follow a high surrogate. Unicode low surrogate must follow a high surrogate. Usage:
  %s [OPTION]... BACKUPDIR

 WAL parsing failed for timeline %u \u0000 cannot be converted to text. backup successfully verified
 both path name and encoded path name cannot duplicate null pointer (internal error)
 cannot specify both %s and %s checksum mismatch for file "%s" checksum without algorithm could not close directory "%s": %m could not close file "%s": %m could not decode file name could not finalize checksum of file "%s" could not finalize checksum of manifest could not initialize checksum of file "%s" could not initialize checksum of manifest could not open directory "%s": %m could not open file "%s": %m could not parse backup manifest: %s could not parse end LSN could not parse start LSN could not read file "%s": %m could not read file "%s": read %d of %lld could not stat file "%s": %m could not stat file or directory "%s": %m could not update checksum of file "%s" could not update checksum of manifest detail:  duplicate path name in backup manifest: "%s" error:  expected at least 2 lines expected version indicator file "%s" has checksum of length %d, but expected %d file "%s" should contain %zu bytes, but read %zu bytes file size is not an integer hint:  invalid checksum for file "%s": "%s" invalid manifest checksum: "%s" last line not newline-terminated manifest checksum mismatch manifest ended unexpectedly manifest has no checksum missing end LSN missing path name missing size missing start LSN missing timeline no backup directory specified out of memory out of memory
 parsing failed program "%s" is needed by %s but was not found in the same directory as "%s" program "%s" was found by "%s" but was not the same version as %s timeline is not an integer too many command-line arguments (first is "%s") unexpected WAL range field unexpected array end unexpected array start unexpected file field unexpected manifest version unexpected object end unexpected object field unexpected object start unexpected scalar unrecognized checksum algorithm: "%s" unrecognized top-level field warning:  Project-Id-Version: pg_verifybackup (PostgreSQL) 16
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2024-11-09 06:05+0000
PO-Revision-Date: 2023-05-22 12:06+0200
Last-Translator: Carlos Chapi <carloswaldo@babelruins.org>
Language-Team: PgSQL-es-ayuda <pgsql-es-ayuda@lists.postgresql.org>
Language: es
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=2; plural=(n != 1);
X-Generator: Poedit 2.4.3
 
Reporte errores a <%s>.
   -?, --help                  muestra esta ayuda, luego salir
   -P, --progress              mostrar información de progreso
   -V, --version               mostrar la información de la versión, luego salir
   -e, --exit-on-error         salir inmediatamente en caso de error
   -i, --ignore=RELATIVE_PATH  ignorar la ruta indicada
   -m, --manifest-path=PATH    usar la ruta especificada para el manifiesto
   -n, --no-parse-wal          no intentar analizar archivos WAL
   -q, --quiet                 no escribir ningún mensaje, excepto errores
   -s, --skip-checksums        omitir la verificación de la suma de comprobación
   -w, --wal-directory=PATH    utilizar la ruta especificada para los archivos WAL
 "%s" tiene un tamaño %lld en el disco pero un tamaño %zu en el manifiesto "%s" no es un archivo o directorio "%s" está presente en el manifiesto pero no en el disco "%s" está presente en el disco pero no en el manifiesto «\u» debe ser seguido por cuatro dígitos hexadecimales. %*s/%s kB (%d%%) verificados Sitio web de %s: <%s>
 %s verifica una copia de seguridad con el fichero de manifiesto de la copia de seguridad.

 Los caracteres con valor 0x%02x deben ser escapados. La secuencia de escape «%s» no es válida. Se esperaba «,» o «]», se encontró «%s». Se esperaba «,» o «}», se encontró «%s». Se esperaba «:», se encontró «%s». Se esperaba un valor JSON, se encontró «%s». Se esperaba un elemento de array o «]», se encontró «%s». Se esperaba el fin de la entrada, se encontró «%s». Se esperaba una cadena o «}», se encontró «%s». Se esperaba una cadena, se encontró «%s». Opciones:
 La cadena de entrada terminó inesperadamente. El elemento «%s» no es válido. Pruebe «%s --help» para mayor información. El valor de escape Unicode no pudo ser traducido a la codificación del servidor %s. Los valores de escape Unicode no se pueden utilizar para valores de código superiores a 007F cuando la codificación no es UTF8. Un «high-surrogate» Unicode no puede venir después de un «high-surrogate». Un «low-surrogate» Unicode debe seguir a un «high-surrogate». Uso:
  %s [OPCIÓN]... BACKUPDIR

 Error al interpretar el WAL para el timeline %u \u0000 no puede ser convertido a text. copia de seguridad verificada correctamente
 hay ambos ruta de archivo (path name) y ruta codificada (encoded path name) no se puede duplicar un puntero nulo (error interno)
 no se puede especificar %s junto con %s no coincide la suma de comprobación para el archivo "%s" suma de comprobación sin algoritmo no se pudo abrir el directorio «%s»: %m no se pudo cerrar el archivo «%s»: %m no se pudo decodificar el nombre del archivo no se pudo finalizar la suma de verificación para el archivo «%s» no se pudo finalizar la suma de verificación del manifiesto no se pudo inicializar la suma de verificación para el archivo «%s» no se pudo inicializar la suma de verificación del manifiesto no se pudo abrir el directorio «%s»: %m no se pudo abrir el archivo «%s»: %m no se pudo analizar el manifiesto de la copia de seguridad: %s no se pudo interpretar el LSN de término no se pudo interpretar el LSN de inicio no se pudo leer el archivo «%s»: %m no se pudo leer el archivo «%s»: leídos %d de %lld no se pudo hacer stat al archivo «%s»: %m no se pudo hacer stat al archivo o directorio «%s»: %m no se pudo actualizar la suma de verificación para el archivo «%s» no se pudo actualizar la suma de verificación del manifiesto detalle:  nombre de ruta duplicado en el manifiesto de la copia de seguridad: "%s" error:  esperado al menos 2 líneas se esperaba indicador de versión el archivo "%s" tiene una suma de comprobación de longitud %d, pero se esperaba %d el archivo "%s" debe contener %zu bytes, pero se leyeron %zu bytes el tamaño del archivo no es un número entero consejo:  suma de comprobación no válida para el archivo "%s": "%s" suma de comprobación de manifiesto no válida: "%s" última línea no termina en nueva línea discordancia en la suma de comprobación del manifiesto el manifiesto terminó inesperadamente el manifiesto no tiene suma de comprobación falta el LSN de término ruta de archivo faltante tamaño faltante falta el LSN de inicio falta el timeline no fue especificado el directorio de respaldo memoria agotada memoria agotada
 procesamiento falló el programa «%s» es requerido por %s, pero no pudo encontrarlo en el mismo directorio que «%s» el programa «%s» fue encontrado por «%s», pero no es de la misma versión que %s el timeline no es un número entero demasiados argumentos en la línea de órdenes (el primero es «%s») campo de rango de WAL inesperado fin de array inesperado inicio de array inesperado campo de archivo inesperado versión de manifiesto inesperada fin de objeto inesperado campo de objeto inesperado inicio de objeto inesperado escalar inesperado algoritmo de suma de comprobación no reconocido: "%s" campo de nivel superior no reconocido precaución:  