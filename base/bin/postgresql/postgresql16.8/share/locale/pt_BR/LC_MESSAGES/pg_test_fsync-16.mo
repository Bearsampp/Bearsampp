��    $      <  5   \      0  1   1  2   c  /   �     �  8   �          4     O     j     �     �     �  (   �  U   �  [   F  K   �  c   �     R  .   m  F   �  E   �  %   )  +   O  !   {     �     �     �     �     �     �     �        /     	   5     ?  �  L  Q   
  S   T
  7   �
  &   �
  I        Q     p     �     �     �  $   �       ,     b   I  n   �  Z     n   v     �  /     E   5  D   {  6   �  )   �  #   !  "   E  	   h     r     y     �  #   �     �     �  8   �     �     �     "          
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
 could not create thread for alarm could not open output file detail:  error:  fsync failed hint:  invalid argument for option %s n/a n/a* too many command-line arguments (first is "%s") warning:  write failed Project-Id-Version: PostgreSQL 16
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2024-01-02 13:02-0300
PO-Revision-Date: 2023-09-27 18:50-0300
Last-Translator: Euler Taveira <euler@eulerto.com>
Language-Team: Brazilian Portuguese <pgsql-translators@postgresql.org>
Language: pt_BR
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=2; plural=(n>1);
 
Compara métodos de sincronização de arquivos utilizando uma escrita de %dkB:
 
Compara métodos de sincronização de arquivos utilizando duas escritas de %dkB:
 
Compara open_sync com diferentes tamanhos de escrita:
 
Escritas de %dkB não sincronizadas:
 
Testar se o fsync em um descritor de arquivo sem escrita é respeitado:
  1 * escrita de 16kB open_sync  2 *  escritas de 8kB open_sync  4 *  escritas de 4kB open_sync  8 *  escritas de 2kB open_sync %13.3f ops/seg  %6.0f usecs/op
 %s deve estar no intervalo de %u..%u %s: %m %u segundo por teste
 %u segundos por teste
 (Se os tempos são similares, fsync() pode sincronizar dados escritos em um
descritor diferente.)
 (Isso é projetado para comparar o custo de escrita de 16jB em diferentes tamanhos
de escrita com open_sync.)
 (em ordem de preferência do wal_sync_method, exceto fdatasync que é o padrão do Linux)
 * Este sistema de arquivos e suas opções de montagem não suportam
  direct I/O, e.g. ext4 em modo journal.
 16 *  escritas de 1kB open_sync Direct I/O não é suportado nesta plataforma.
 F_NOCACHE suportado nesta plataforma para open_datasync e open_sync.
 O_DIRECT suportado nesta plataforma para open_datasync e open_sync.
 Tente "%s --help" para obter informações adicionais. Uso: %s [-f ARQUIVO] [-s SEGS-POR-TESTE]
 não pôde criar thread para alarme não pôde abrir arquivo de saída detalhe:  erro:  fsync falhou dica:  argumento inválido para opção %s n/a n/a* muitos argumentos de linha de comando (primeiro é "%s") aviso:  escrita falhou 