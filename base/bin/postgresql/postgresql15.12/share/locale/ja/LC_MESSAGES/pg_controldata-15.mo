��    ]           �      �  X   �  
   B     M  3   f  ?   �  (   �  C   	     G	     [	     k	  ,   o	  ,   �	  )   �	  )   �	  )   
  )   G
  )   q
  )   �
  +   �
  )   �
  )     ,   E  )   r  ,   �  )   �  )   �  )     ,   G  )   t  )   �  ,   �  )   �  )     )   I  )   s  )   �  )   �  )   �  )     )   E  )   o  )   �  )   �  )   �  )     ,   A  )   n     �  )   �  >  �  )     %   A     g  )   o  �   �  "   `     �     �     �     �     �  (   �          2  (   O     x     �     �     �  )   �  )   �  )     )   H  )   r     �     �     �     �  )   �  )   �      	        &     <     J  /   V  )   �     �     �  )   �  )   
     4  �  8  o        �     �  >   �  D   �  5   >  T   t     �     �     �  =   �  =   :  F   x  =   �  ;   �  B   9  >   |  =   �  ?   �  :   9  ?   t  @   �  6   �  ?   ,  ;   l  ;   �  ;   �  >      ;   _  ?   �  @   �  >      ;   [   ;   �   ;   �   ;   !  <   K!  ;   �!  <   �!  ;   "  <   ="  <   z"  7   �"  :   �"  A   *#  =   l#  6   �#  ,   �#  A   $  �   P$  <   >%  2   {%     �%  8   �%    �%  -   �&     -'  	   :'     D'  ;   c'  4   �'  M   �'  ;   "(  8   ^(  q   �(  5   	)     ?)     ^)  	   })  3   �)  3   �)  3   �)  3   #*  3   W*  	   �*  9   �*     �*     �*  4   �*  7   +  �  J+     �,  6   �,     -     <-  ?   L-  3   �-  !   �-  "   �-  3   .  3   9.     m.     5            -   :               G   [   4                     1           $   J       ]   @                         !   2                  =       '   
       C         E   \   >   ;       "   &          D   Q          U   #   Y   <   L       3   Z      /       ,   	   %   8          (   N   I            7   H           V   .   0   9                  X   A              K   F   B      +   S   P      R   O   T   *      6      W   ?                                  )   M    
If no data directory (DATADIR) is specified, the environment variable PGDATA
is used.

 
Options:
   %s [OPTION] [DATADIR]
   -?, --help             show this help, then exit
   -V, --version          output version information, then exit
  [-D, --pgdata=]DATADIR  data directory
 %s displays control information of a PostgreSQL database cluster.

 %s home page: <%s>
 64-bit integers ??? Backup end location:                  %X/%X
 Backup start location:                %X/%X
 Blocks per segment of large relation: %u
 Bytes per WAL segment:                %u
 Catalog version number:               %u
 Data page checksum version:           %u
 Database block size:                  %u
 Database cluster state:               %s
 Database system identifier:           %llu
 Date/time type storage:               %s
 End-of-backup record required:        %s
 Fake LSN counter for unlogged rels:   %X/%X
 Float8 argument passing:              %s
 Latest checkpoint location:           %X/%X
 Latest checkpoint's NextMultiOffset:  %u
 Latest checkpoint's NextMultiXactId:  %u
 Latest checkpoint's NextOID:          %u
 Latest checkpoint's NextXID:          %u:%u
 Latest checkpoint's PrevTimeLineID:   %u
 Latest checkpoint's REDO WAL file:    %s
 Latest checkpoint's REDO location:    %X/%X
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
 Min recovery ending loc's timeline:   %u
 Minimum recovery ending location:     %X/%X
 Mock authentication nonce:            %s
 Report bugs to <%s>.
 Size of a large-object chunk:         %u
 The WAL segment size stored in the file, %d byte, is not a power of two
between 1 MB and 1 GB.  The file is corrupt and the results below are
untrustworthy.

 The WAL segment size stored in the file, %d bytes, is not a power of two
between 1 MB and 1 GB.  The file is corrupt and the results below are
untrustworthy.

 Time of latest checkpoint:            %s
 Try "%s --help" for more information. Usage:
 WAL block size:                       %u
 WARNING: Calculated CRC checksum does not match value stored in file.
Either the file is corrupt, or it has a different layout than this program
is expecting.  The results below are untrustworthy.

 WARNING: invalid WAL segment size
 by reference by value byte ordering mismatch could not close file "%s": %m could not fsync file "%s": %m could not open file "%s" for reading: %m could not open file "%s": %m could not read file "%s": %m could not read file "%s": read %d of %zu could not write file "%s": %m in archive recovery in crash recovery in production max_connections setting:              %d
 max_locks_per_xact setting:           %d
 max_prepared_xacts setting:           %d
 max_wal_senders setting:              %d
 max_worker_processes setting:         %d
 no no data directory specified off on pg_control last modified:             %s
 pg_control version number:            %u
 possible byte ordering mismatch
The byte ordering used to store the pg_control file might not match the one
used by this program.  In that case the results below would be incorrect, and
the PostgreSQL installation would be incompatible with this data directory. shut down shut down in recovery shutting down starting up too many command-line arguments (first is "%s") track_commit_timestamp setting:       %s
 unrecognized status code unrecognized wal_level wal_level setting:                    %s
 wal_log_hints setting:                %s
 yes Project-Id-Version: pg_controldata (PostgreSQL 15)
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2022-08-09 12:00+0900
PO-Revision-Date: 2022-05-10 13:48+0900
Last-Translator: Kyotaro Horiguchi <horikyota.ntt@gmail.com>
Language-Team: Japan PostgreSQL Users Group <jpug-doc@ml.postgresql.jp>
Language: ja
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=1; plural=0;
X-Generator: Poedit 1.8.13
 
データディレクトリ(DATADIR)が指定されない場合、PGDATA環境変数が使用されます。

 
オプション:
   %s [OPTION] [DATADIR]
   -?, --help             このヘルプを表示して終了
   -V, --version          バージョン情報を表示して終了
  [-D, --pgdata=]DATADIR  データディレクトリ
 %s はPostgreSQLデータベースクラスタの制御情報を表示します。

 %s ホームページ: <%s>
 64ビット整数 ??? バックアップ終了位置:                        %X/%X
 バックアップ開始位置:                        %X/%X
 大きなリレーションのセグメント毎のブロック数:%u
 WALセグメント当たりのバイト数:               %u
 カタログバージョン番号:                      %u
 データベージチェックサムのバージョン:        %u
 データベースのブロックサイズ:                %u
 データベースクラスタの状態:                  %s
 データベースシステム識別子:                  %llu
 日付/時刻型の格納方式:                       %s
 必要なバックアップ最終レコード:              %s
 UNLOGGEDリレーションの偽のLSNカウンタ:       %X/%X
 Float8引数の渡し方:                          %s
 最終チェックポイント位置:                    %X/%X
 最終チェックポイントのNextMultiOffset:       %u
 最終チェックポイントのNextMultiXactId:       %u
 最終チェックポイントのNextOID:               %u
 最終チェックポイントのNextXID:               %u:%u
 最終チェックポイントのPrevTimeLineID:        %u
 最終チェックポイントのREDO WALファイル:      %s
 最終チェックポイントのREDO位置:              %X/%X
 最終チェックポイントの時系列ID:              %u
 最終チェックポイントのfull_page_writes:      %s
 最終チェックポイントのnewestCommitTsXid:     %u
 最終チェックポイントのoldestActiveXID:       %u
 最終チェックポイントのoldestCommitTsXid:     %u
 最終チェックポイントのoldestMultiのDB:       %u
 最終チェックポイントのoldestMultiXid:        %u
 最終チェックポイントのoldestXIDのDB:         %u
 最終チェックポイントのoldestXID:             %u
 インデックス内の最大列数:                    %u
 最大データアラインメント:                    %u
 識別子の最大長:                              %u
 TOASTチャンクの最大サイズ:                   %u
 最小リカバリ終了位置のタイムライン:          %u
 最小リカバリ終了位置:                        %X/%X
 認証用の疑似nonce:                           %s
 バグは<%s>に報告してください。
 ラージオブジェクトチャンクのサイズ:          %u
 ファイル中のWALセグメントサイズは %d バイトとなっていますが、これは
1MBから1GBまでの2の累乗ではありません。このファイルは壊れており、
以下の情報は信頼できません。

 最終チェックポイント時刻:                    %s
 詳細は"%s --help"を実行してください。 使用方法:
 WALのブロックサイズ:                         %u
 警告: CRCチェックサムの計算結果がファイル内の値と一致しません。
ファイルの破損、あるいは、本プログラムが想定するレイアウトと異なる
可能性があります。以下の結果は信頼できません。

 警告: 不正なWALセグメントサイズ
 参照渡し 値渡し バイトオーダの不整合 ファイル"%s"をクローズできませんでした: %m ファイル"%s"をfsyncできませんでした: %m ファイル"%s"を読み取り用にオープンできませんでした: %m ファイル"%s"をオープンできませんでした: %m ファイル"%s"の読み取りに失敗しました: %m ファイル"%1$s"を読み込めませんでした: %3$zuバイトのうち%2$dバイトを読み込みました ファイル"%s"を書き出せませんでした: %m アーカイブリカバリ中 クラッシュリカバリ中 運用中 max_connectionsの設定:                       %d
 max_locks_per_xactの設定:                    %d
 max_prepared_xactsの設定:                    %d
 max_wal_sendersの設定:                       %d
 max_worker_processesの設定:                  %d
 いいえ データディレクトリが指定されていません オフ オン pg_control最終更新:                          %s
 pg_controlバージョン番号:                    %u
 バイトオーダが異なる可能性があります。
pg_controlファイルを格納するために使用するバイトオーダが本プログラムで使用
されるものと一致しないようです。この場合以下の結果は不正確になります。また、
PostgreSQLインストレーションはこのデータディレクトリと互換性がなくなります。 シャットダウン リカバリ中にシャットダウンされている シャットダウン処理中 起動処理中 コマンドライン引数が多すぎます。(先頭は"%s") track_commit_timestampの設定:                %s
 未知のステータスコード wal_level を認識できません wal_levelの設定:                             %s
 wal_log_hintsの設定:                         %s
 はい 