��    v      �  �   |      �	     �	  9   
     E
  F   \
  =   �
  D   �
  I   &  �   p  A   /  ;   q  M   �  K   �  K   G  0   �  =   �  ;     2   >     q  +   �     �  )   �  )   �  )        ?  )   \  )   �  +   �  )   �  R     )   Y  )   �     �  U   �  A      )   b  )   �  )   �  ,   �  )     )   7  )   a  )   �  )   �  )   �  )   	  )   3  )   ]  )   �  )   �  )   �  )     )   /  )   Y  )   �  )   �  )   �       )     )   B  )   l  )   �  	   �  )   �  �   �  &   �  !   �  )   �       ,     *   L  A   w     �     �     �  @   �  '   -  &   U  "   |  1   �     �  7   �  +   (  !   T  (   v     �  ,   �  :   �  !   $     F  0   c  8   �     �  "   �                    .     M  &   c  +   �  )   �     �     �  -      >   .  )   m     �  ;   �  =   �  �     )   �  /   �  B     7   N  (   �     �  	   �  �  �     �   T   �       !  O    !  B   p!  x   �!  B   ,"    o"  �   �#  9   $  �   K$  �   �$     v%  =   �%  |   4&  w   �&  G   )'     q'  :   �'     �'  ;   �'  ;   (  <   Q(  #   �(  ;   �(  <   �(  ?   +)  ;   k)  i   �)  =   *  ;   O*  #   �*  o   �*  O   +  <   o+  <   �+  <   �+  ?   &,  <   f,  <   �,  <   �,  <   -  <   Z-  >   �-  <   �-  <   .  <   P.  ;   �.  <   �.  ;   /  ;   B/  )   ~/  )   �/  )   �/  )   �/  )   &0     P0  )   o0  )   �0  )   �0  )   �0  
   1  ;   "1  �   ^1  2   B2  (   u2  ;   �2  /   �2  A   
3  0   L3  R   }3     �3  
   �3  *   �3  Q   4  .   h4  4   �4  .   �4  8   �4  -   45  N   b5  @   �5  .   �5  ;   !6  *   ]6  ;   �6  ?   �6  ,   7  (   17  7   Z7  O   �7  -   �7  2   8  	   C8     M8     V8  "   h8  !   �8  ;   �8  ;   �8  )   %9  &   O9     v9  J   �9  U   �9  )   #:     M:  C   U:  D   �:  �   �:  <   �;  B   �;  J   <  F   `<  I   �<     �<     =     <      j   Z       [   ]   T   8   o           i   m           ,       1   L   0             +   A   N   -   l   H   u      '      O                 7   &          V   S      n                 /           W   >   G   (   4   g   D              #   6           \   b       q   s      %   9       M       3   r          .   	   )   h   @      d           5   *       p   !   $   f                Y   
      ?           =   2                   t   J       B       P   k      e   X   ^       c   C   F           a   Q   I         :   ;   v   R   U                      E          K   `   _       "    

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
 Try "%s --help" for more information.
 Usage:
  %s [OPTION]... DATADIR

 WAL block size:                       %u
 Write-ahead log reset
 You must run %s as the PostgreSQL superuser. argument of --wal-segsize must be a number argument of --wal-segsize must be a power of 2 between 1 and 1024 by reference by value cannot be executed by "root" cannot create restricted tokens on this platform: error code %lu could not allocate SIDs: error code %lu could not change directory to "%s": %m could not close directory "%s": %m could not create restricted token: error code %lu could not delete file "%s": %m could not get exit code from subprocess: error code %lu could not load library "%s": error code %lu could not open directory "%s": %m could not open file "%s" for reading: %m could not open file "%s": %m could not open process token: error code %lu could not re-execute with restricted token: error code %lu could not read directory "%s": %m could not read file "%s": %m could not read permissions of directory "%s": %m could not start process for command "%s": error code %lu could not write file "%s": %m data directory is of wrong version error:  fatal:  fsync error: %m invalid argument for option %s lock file "%s" exists multitransaction ID (-m) must not be 0 multitransaction offset (-O) must not be -1 newestCommitTsXid:                    %u
 no data directory specified off oldest multitransaction ID (-m) must not be 0 oldest transaction ID (-u) must be greater than or equal to %u oldestCommitTsXid:                    %u
 on pg_control exists but has invalid CRC; proceed with caution pg_control exists but is broken or wrong version; ignoring it pg_control specifies invalid WAL segment size (%d byte); proceed with caution pg_control specifies invalid WAL segment size (%d bytes); proceed with caution pg_control version number:            %u
 too many command-line arguments (first is "%s") transaction ID (-c) must be either 0 or greater than or equal to 2 transaction ID (-x) must be greater than or equal to %u transaction ID epoch (-e) must not be -1 unexpected empty file "%s" warning:  Project-Id-Version: PostgreSQL 14
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2021-08-27 02:17+0000
PO-Revision-Date: 2024-09-16 16:36+0200
Last-Translator: Guillaume Lelarge <guillaume@lelarge.info>
Language-Team: French <guillaume@lelarge.info>
Language: fr
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
X-Generator: Poedit 3.5
Plural-Forms: nplurals=2; plural=(n > 1);
 

Valeurs à changer :

 
Si ces valeurs semblent acceptables, utiliser -f pour forcer la
réinitialisation.
 
Rapporter les bogues à <%s>.
       --wal-segsize=TAILLE         configure la taille des segments WAL, en Mo
   -?, --help                       affiche cette aide puis quitte
   -O, --multixact-offset=DÉCALAGE  configure le prochain décalage
                                   multitransaction
   -V, --version                    affiche la version puis quitte
   -c, --commit-timestamp-ids=XID,XID
                                   configure les transactions la plus ancienne
                                   et la plus récente contenant les dates/heures
                                   de validation (zéro signifie aucun changement)
   -e, --epoch=XIDEPOCH             configure la valeur epoch du prochain
                                   identifiant de transaction
   -f, --force                      force la mise à jour
   -l, --next-wal-file=FICHIERWAL   configure l'emplacement minimal de début
                                   des WAL du nouveau journal de transactions
   -m, --multixact-ids=MXID,MXID    configure le prochain et le plus ancien
                                   identifiants multi-transactions
   -n, --dry-run                    pas de mise à jour, affiche
                                   simplement ce qui sera fait
   -o, --next-oid=OID               configure le prochain OID
   -u, --oldest-transaction-id=XID  configure l'identifiant de transaction le
                                   plus ancien
   -x, --next-transaction-id=XID    configure le prochain identifiant de
                                   transaction
   [-D, --pgdata=]RÉP_DONNEES       répertoire de la base de données
 Page d'accueil de %s : <%s>
 %s réinitialise le journal des transactions PostgreSQL.

 entiers 64-bits Blocs par segment des relations volumineuses :          %u
 Octets par segment du journal de transaction :          %u
 Numéro de version du catalogue :                        %u
 Valeurs actuelles de pg_control :

 Version des sommes de contrôle des pages de données : %u
 Taille du bloc de la base de données :                  %u
 Identifiant du système de base de données :             %llu
 Stockage du type date/heure :                           %s
 Le fichier « %s » contient « %s », qui n'est pas compatible avec la version « %s » de ce programme. Premier segment du journal après réinitialisation :     %s
 Passage d'argument float8 :                             %s
 Valeurs de pg_control devinées :

 Si vous êtes certain que le chemin du répertoire de données est correct, exécuter
  touch %s
et réessayer. Le serveur est-il démarré ? Sinon, supprimer le fichier verrou et réessayer. Dernier NextMultiOffset du point de contrôle :          %u
 Dernier NextMultiXactId du point de contrôle :          %u
 Dernier NextOID du point de contrôle :                  %u
 Dernier NextXID du point de contrôle :                  %u:%u
 Dernier TimeLineID du point de contrôle :               %u
 Dernier full_page_writes du point de contrôle :         %s
 Dernier newestCommitTsXid du point de contrôle :        %u
 Dernier oldestActiveXID du point de contrôle :          %u
 Dernier oldestCommitTsXid du point de contrôle :        %u
 Dernier oldestMulti du point de contrôle de la base :     %u
 Dernier oldestMultiXid du point de contrôle :           %u
 Dernier oldestXID du point de contrôle de la base :     %u
 Dernier oldestXID du point de contrôle :                %u
 Nombre maximum de colonnes d'un index:                  %u
 Alignement maximal des données :                        %u
 Longueur maximale des identifiants :                    %u
 Longueur maximale d'un morceau TOAST :                  %u
 NextMultiOffset:                      %u
 NextMultiXactId:                      %u
 NextOID:                              %u
 NextXID Epoch:                        %u
 NextXID:                              %u
 l'OID (-o) ne doit pas être 0 OldestMulti's DB:                     %u
 OldestMultiXid:                       %u
 OldestXID's DB:                       %u
 OldestXID:                            %u
 Options :
 Taille d'un morceau de Large Object :                   %u
 Le serveur de bases de données n'a pas été arrêté proprement.
Ré-initialiser le journal des transactions peut occasionner des pertes de
données.
Pour continuer malgré tout, utiliser -f pour forcer la
réinitialisation.
 Essayer « %s --help » pour plus d'informations.
 Usage :
  %s [OPTION]... RÉP_DONNÉES

 Taille de bloc du journal de transaction :              %u
 Réinitialisation des journaux de transactions
 Vous devez exécuter %s en tant que super-utilisateur PostgreSQL. l'argument de --wal-segsize doit être un nombre l'argument de --wal-segsize doit être une puissance de 2 comprise entre 1 et 1024 par référence par valeur ne peut pas être exécuté par « root » ne peut pas créer les jetons restreints sur cette plateforme : code d'erreur %lu n'a pas pu allouer les SID : code d'erreur %lu n'a pas pu modifier le répertoire par « %s » : %m n'a pas pu fermer le répertoire « %s » : %m n'a pas pu créer le jeton restreint : code d'erreur %lu n'a pas pu supprimer le fichier « %s » : %m n'a pas pu récupérer le code de statut du sous-processus : code d'erreur %lu n'a pas pu charger la bibliothèque « %s » : code d'erreur %lu n'a pas pu ouvrir le répertoire « %s » : %m n'a pas pu ouvrir le fichier « %s » pour une lecture : %m n'a pas pu ouvrir le fichier « %s » : %m n'a pas pu ouvrir le jeton du processus : code d'erreur %lu n'a pas pu ré-exécuter le jeton restreint : code d'erreur %lu n'a pas pu lire le répertoire « %s » : %m n'a pas pu lire le fichier « %s » : %m n'a pas pu lire les droits du répertoire « %s » : %m n'a pas pu démarrer le processus pour la commande « %s » : code d'erreur %lu impossible d'écrire le fichier « %s » : %m le répertoire des données a une mauvaise version erreur :  fatal :  erreur fsync : %m argument invalide pour l'option %s le fichier verrou « %s » existe l'identifiant de multi-transaction (-m) ne doit pas être 0 le décalage de multi-transaction (-O) ne doit pas être -1 newestCommitTsXid:                    %u
 aucun répertoire de données indiqué désactivé l'identifiant de multi-transaction le plus ancien (-m) ne doit pas être 0 l'identifiant de transaction le plus ancien (-u) doit être supérieur ou égal à %u oldestCommitTsXid:                    %u
 activé pg_control existe mais son CRC est invalide ; agir avec précaution pg_control existe mais est corrompu ou de mauvaise version ; ignoré pg_control spécifie une taille invalide de segment WAL (%d octet) ; agir avec précaution pg_control spécifie une taille invalide de segment WAL (%d octets) ; agir avec précaution Numéro de version de pg_control :                       %u
 trop d'arguments en ligne de commande (le premier étant « %s ») l'identifiant de transaction (-c) doit être 0 ou supérieur ou égal à 2 l'identifiant de transaction (-x) doit être supérieur ou égal à %u la valeur epoch de l'identifiant de transaction (-e) ne doit pas être -1 fichier vide inattendu « %s » attention :  