��    ]           �      �  X   �  
   B     M  3   f  ?   �  (   �  C   	     G	     [	     k	  ,   o	  ,   �	  )   �	  )   �	  )   
  )   G
  )   q
  )   �
  +   �
  )   �
  )     ,   E  )   r  ,   �  )   �  )   �  )     ,   G  )   t  )   �  ,   �  )   �  )     )   I  )   s  )   �  )   �  )   �  )     )   E  )   o  )   �  )   �  )   �  )     ,   A  )   n     �  )   �  >  �  )     %   A     g  )   o  �   �  "   `     �     �     �     �     �  (   �          2  (   O     x     �     �     �  )   �  )   �  )     )   H  )   r     �     �     �     �  )   �  )   �      	        &     <     J  /   V  )   �     �     �  )   �  )   
     4  �  8  i   �     `     l  >   �  >   �  C   	  D   M     �     �     �  >   �  ?     ;   B  ;   ~  <   �  =   �  <   5  =   r  ?   �  ;   �  ;   ,  ?   h  ;   �  ?   �  <   $  <   a  <   �  ?   �  <     <   X  ?   �  <   �  <      <   O   <   �   <   �   <   !  <   C!  <   �!  <   �!  ;   �!  <   6"  ;   s"  ;   �"  ;   �"  @   '#  0   h#     �#  ;   �#  �  �#  <   �%  1   �%     &  ;   &    S&  +   b'     �'  
   �'  !   �'  *   �'  C   �'  ;   :(  *   v(  (   �(  5   �(  -    )  .   .)  1   ])     �)  <   �)  <   �)  <   *  <   T*  <   �*     �*  &   �*     �*     +  <   +  <   J+  8  �+     �,     �,     �,     �,  B   
-  <   M-     �-     �-  <   �-  <   �-     1.     5            -   :               G   [   4                     1           $   J       ]   @                         !   2                  =       '   
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
 yes Project-Id-Version: PostgreSQL 15
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2022-04-12 05:16+0000
PO-Revision-Date: 2024-09-16 16:35+0200
Last-Translator: Guillaume Lelarge <guillaume@lelarge.info>
Language-Team: French <guillaume@lelarge.info>
Language: fr
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=2; plural=(n > 1);
X-Generator: Poedit 3.5
 
Si aucun répertoire (RÉP_DONNÉES) n'est indiqué, la variable d'environnement
PGDATA est utilisée.

 
Options :
   %s [OPTION] [RÉP_DONNÉES]
   -?, --help                   affiche cette aide puis quitte
   -V, --version                affiche la version puis quitte
   [-D, --pgdata] RÉP_DONNEES   répertoire de la base de données
 %s affiche les informations de contrôle de l'instance PostgreSQL.

 Page d'accueil de %s : <%s>
 entiers 64-bits ??? Fin de la sauvegarde :                                  %X/%X
 Début de la sauvegarde :                                %X/%X
 Blocs par segment des relations volumineuses :          %u
 Octets par segment du journal de transaction :          %u
 Numéro de version du catalogue :                        %u
 Version des sommes de contrôle des pages de données :   %u
 Taille du bloc de la base de données :                  %u
 État du cluster de base de données :                    %s
 Identifiant du système de base de données :             %llu
 Stockage du type date/heure :                           %s
 Enregistrement de fin de sauvegarde requis :            %s
 Faux compteur LSN pour les relations non journalisés :  %X/%X
 Passage d'argument float8 :                             %s
 Dernier point de contrôle :                             %X/%X
 Dernier NextMultiOffset du point de contrôle :          %u
 Dernier NextMultiXactId du point de contrôle :          %u
 Dernier NextOID du point de contrôle :                  %u
 Dernier NextXID du point de contrôle :                  %u:%u
 Dernier PrevTimeLineID du point de contrôle :           %u
 Dernier fichier WAL du rejeu du point de contrôle :     %s
 Dernier REDO (reprise) du point de contrôle :           %X/%X
 Dernier TimeLineID du point de contrôle :               %u
 Dernier full_page_writes du point de contrôle :         %s
 Dernier newestCommitTsXid du point de contrôle :        %u
 Dernier oldestActiveXID du point de contrôle :          %u
 Dernier oldestCommitTsXid du point de contrôle :        %u
 Dernier oldestMulti du point de contrôle de base :      %u
 Dernier oldestMultiXid du point de contrôle :           %u
 Dernier oldestXID du point de contrôle de la base :     %u
 Dernier oldestXID du point de contrôle :                %u
 Nombre maximum de colonnes d'un index:                  %u
 Alignement maximal des données :                        %u
 Longueur maximale des identifiants :                    %u
 Longueur maximale d'un morceau TOAST :                  %u
 Timeline de l'emplacement de fin de restauration :      %u
 Emplacement de fin de la récupération minimale :        %X/%X
 Nonce pour simuler une identité:            %s
 Rapporter les bogues à <%s>.
 Taille d'un morceau de Large Object :                   %u
 La taille d'un segment WAL enregistré dans le fichier, %d octet, n'est pas une puissance de deux entre 1 Mo et 1 Go. Le fichier est corrompu et les résultats ci-dessous ne proviennent pas d'une source fiable.

 La taille d'un segment WAL enregistré dans le fichier, %d octets, n'est pas une puissance de deux entre 1 Mo et 1 Go. Le fichier est corrompu et les résultats ci-dessous ne proviennent pas d'une source fiable.

 Heure du dernier point de contrôle :                    %s
 Essayez « %s --help » pour plus d'informations. Usage :
 Taille de bloc du journal de transaction :              %u
 ATTENTION : Les sommes de contrôle (CRC) calculées ne correspondent pas aux
valeurs stockées dans le fichier.
Soit le fichier est corrompu, soit son organisation diffère de celle
attendue par le programme.
Les résultats ci-dessous ne sont pas dignes de confiance.

 ATTENTION : taille invalide du segment WAL
 par référence par valeur différence de l'ordre des octets n'a pas pu fermer le fichier « %s » : %m n'a pas pu synchroniser sur disque (fsync) le fichier « %s » : %m n'a pas pu ouvrir le fichier « %s » pour une lecture : %m n'a pas pu ouvrir le fichier « %s » : %m n'a pas pu lire le fichier « %s » : %m n'a pas pu lire le fichier « %s » : a lu %d sur %zu impossible d'écrire le fichier « %s » : %m restauration en cours (à partir des archives) restauration en cours (suite à un arrêt brutal) en production Paramétrage actuel de max_connections :                 %d
 Paramètrage actuel de max_locks_per_xact :              %d
 Paramètrage actuel de max_prepared_xacts :              %d
 Paramètrage actuel de max_wal_senders :                 %d
 Paramétrage actuel de max_worker_processes :            %d
 non aucun répertoire de données indiqué désactivé activé Dernière modification de pg_control :                   %s
 Numéro de version de pg_control :                       %u
 possible incohérence dans l'ordre des octets
L'ordre des octets utilisé pour enregistrer le fichier pg_control peut ne
pas correspondre à celui utilisé par ce programme. Dans ce cas, les
résultats ci-dessous sont incorrects, et l'installation de PostgreSQL
est incompatible avec ce répertoire des données. arrêt arrêt pendant la restauration arrêt en cours démarrage en cours trop d'arguments en ligne de commande (le premier étant « %s ») Paramètrage actuel de track_commit_timestamp :          %s
 code de statut inconnu wal_level non reconnu Paramétrage actuel de wal_level :                       %s
 Paramétrage actuel de wal_log_hints :                   %s
 oui 