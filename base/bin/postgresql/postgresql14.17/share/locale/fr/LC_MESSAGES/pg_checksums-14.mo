��    8      �  O   �      �  X   �  
   2     =  5   Y  P   �  5   �  A     :   X  2   �  1   �  G   �  3   @  *   t     �  T   �          "     6     J     h     �     �     �     �  k   �  &   V	     }	  a   �	     �	     
  ;   &
     b
  !   |
     �
  (   �
  3   �
       )   5  5   _  .   �  -   �  )   �  "        ?     G  3   O  +   �     �  2   �  !   �  )         J  /   a     �  	   �  �  �  i   o     �  !   �  <     �   D  D   �  <     L   J  @   �  <   �  �     :   �  6   �       e   '     �  #   �     �  1   �  F     B   ]  6   �     �     �  �     2   �     �  �   �  6   h  8   �  B   �       .   ;  *   j  8   �  C   �  *     ;   =  M   y  U   �  Q     Q   o     �  	   �     �  A   �  >   6  &   u  H   �  .   �  >     +   S  B     $   �     �     8          .   )                   !   #               /          4   (   *             ,       0       3      &                               7   6          1            2   '       $                          +              "              
          %             5      -   	        
If no data directory (DATADIR) is specified, the environment variable PGDATA
is used.

 
Options:
   %s [OPTION]... [DATADIR]
   -?, --help               show this help, then exit
   -N, --no-sync            do not wait for changes to be written safely to disk
   -P, --progress           show progress information
   -V, --version            output version information, then exit
   -c, --check              check data checksums (default)
   -d, --disable            disable data checksums
   -e, --enable             enable data checksums
   -f, --filenode=FILENODE  check only relation with specified filenode
   -v, --verbose            output verbose messages
  [-D, --pgdata=]DATADIR    data directory
 %*s/%s MB (%d%%) computed %s enables, disables, or verifies data checksums in a PostgreSQL database cluster.

 %s home page: <%s>
 Bad checksums:  %s
 Blocks scanned: %s
 Checksum operation completed
 Checksums disabled in cluster
 Checksums enabled in cluster
 Data checksum version: %u
 Files scanned:  %s
 Report bugs to <%s>.
 The database cluster was initialized with block size %u, but pg_checksums was compiled with block size %u.
 Try "%s --help" for more information.
 Usage:
 checksum verification failed in file "%s", block %u: calculated checksum %X but block contains %X checksums enabled in file "%s" checksums verified in file "%s" cluster is not compatible with this version of pg_checksums cluster must be shut down could not open directory "%s": %m could not open file "%s": %m could not read block %u in file "%s": %m could not read block %u in file "%s": read %d of %d could not stat file "%s": %m could not write block %u in file "%s": %m could not write block %u in file "%s": wrote %d of %d data checksums are already disabled in cluster data checksums are already enabled in cluster data checksums are not enabled in cluster database cluster is not compatible error:  fatal:  invalid filenode specification, must be numeric: %s invalid segment number %d in file name "%s" no data directory specified option -f/--filenode can only be used with --check pg_control CRC value is incorrect seek failed for block %u in file "%s": %m syncing data directory too many command-line arguments (first is "%s") updating control file warning:  Project-Id-Version: PostgreSQL 14
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2020-12-23 15:18+0000
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
Si aucun répertoire (RÉP_DONNÉES) n'est indiqué, la variable d'environnement
PGDATA est utilisée.

 
Options :
   %s [OPTION]... [RÉP_DONNÉES]
   -?, --help                 affiche cette aide puis quitte
   -N, --no-sync              n'attend pas que les modifications soient
                             proprement écrites sur disque
   -P, --progress             affiche la progression de l'opération
   -V, --version              affiche la version puis quitte
   -c, --check                vérifie les sommes de contrôle (par défaut)
   -d, --disable              désactive les sommes de contrôle
   -e, --enable               active les sommes de contrôle
   -f, --filenode=FILENODE    vérifie seulement la relation dont l'identifiant
                             relfilenode est indiqué
   -v, --verbose              affiche des messages verbeux
  [-D, --pgdata=]REP_DONNEES  répertoire des données
 %*s/%s Mo (%d%%) traités %s active, désactive ou vérifie les sommes de contrôle de données dans
une instance PostgreSQL.

 Page d'accueil de %s : <%s>
 Mauvaises sommes de contrôle : %s
 Blocs parcourus : %s
 Opération sur les sommes de contrôle terminée
 Sommes de contrôle sur les données désactivées sur cette instance
 Sommes de contrôle sur les données activées sur cette instance
 Version des sommes de contrôle sur les données : %u
 Fichiers parcourus : %s
 Rapporter les bogues à <%s>.
 L'instance a été initialisée avec une taille de bloc à %u alors que pg_checksums a été compilé avec une taille de bloc à %u.
 Essayez « %s --help » pour plus d'informations.
 Usage :
 échec de la vérification de la somme de contrôle dans le fichier « %s », bloc %u : somme de contrôle calculée %X, alors que le bloc contient %X sommes de contrôle activées dans le fichier « %s » sommes de contrôle vérifiées dans le fichier « %s » l'instance n'est pas compatible avec cette version de pg_checksums l'instance doit être arrêtée n'a pas pu ouvrir le répertoire « %s » : %m n'a pas pu ouvrir le fichier « %s » : %m n'a pas pu lire le bloc %u dans le fichier « %s » : %m n'a pas pu lire le bloc %u dans le fichier « %s » : %d lus sur %d n'a pas pu tester le fichier « %s » : %m n'a pas pu écrire le bloc %u dans le fichier « %s » : %m n'a pas pu écrire le bloc %u du fichier « %s » : a écrit %d octets sur %d les sommes de contrôle sur les données sont déjà désactivées sur cette instance les sommes de contrôle sur les données sont déjà activées sur cette instance les sommes de contrôle sur les données ne sont pas activées sur cette instance l'instance n'est pas compatible erreur :  fatal :  spécification invalide du relfilnode, doit être numérique : %s numéro de segment %d invalide dans le nom de fichier « %s » aucun répertoire de données indiqué l'option « -f/--filenode » peut seulement être utilisée avec --check la valeur CRC de pg_control n'est pas correcte n'a pas pu rechercher le bloc %u dans le fichier « %s » : %m synchronisation du répertoire des données trop d'arguments en ligne de commande (le premier étant « %s ») mise à jour du fichier de contrôle attention :  