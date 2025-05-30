��          �   %   �      `  �   a  
   ;  �   F     �  3   �  +     7   E  6   }  L   �  <        >  6   R  &   �     �  $   �  )   �  (     (   0     Y     x     �     �     �  !   �     �  	     �    �   �     �	  �   �	     g
  B   �
  .   �
  .   �
  F   (  K   o  @   �     �  B     2   \     �  /   �  :   �  :     8   >  -   w  	   �     �  #   �  *   �  @     %   H     n                                                                                
                   	                         
For use as archive_cleanup_command in postgresql.conf:
  archive_cleanup_command = 'pg_archivecleanup [OPTION]... ARCHIVELOCATION %%r'
e.g.
  archive_cleanup_command = 'pg_archivecleanup /mnt/server/archiverdir %%r'
 
Options:
 
Or for use as a standalone archive cleaner:
e.g.
  pg_archivecleanup /mnt/server/archiverdir 000000010000000000000010.00000020.backup
 
Report bugs to <%s>.
   %s [OPTION]... ARCHIVELOCATION OLDESTKEPTWALFILE
   -?, --help     show this help, then exit
   -V, --version  output version information, then exit
   -d             generate debug output (verbose mode)
   -n             dry run, show the names of the files that would be removed
   -x EXT         clean up files if they have this extension
 %s home page: <%s>
 %s removes older WAL files from PostgreSQL archives.

 Try "%s --help" for more information.
 Usage:
 archive location "%s" does not exist could not close archive location "%s": %m could not open archive location "%s": %m could not read archive location "%s": %m could not remove file "%s": %m error:  fatal:  invalid file name argument must specify archive location must specify oldest kept WAL file too many command-line arguments warning:  Project-Id-Version: PostgreSQL 14
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2020-04-16 06:16+0000
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
Pour utiliser comme archive_cleanup_command dans postgresql.conf :
  archive_cleanup_command = 'pg_archivecleanup [OPTION]... EMPLACEMENTARCHIVE %%r'
Par exemple :
  archive_cleanup_command = 'pg_archivecleanup /mnt/serveur/reparchives %%r'
 
Options :
 
Ou pour utiliser comme nettoyeur autonome d'archives :
Par exemple :
  pg_archivecleanup /mnt/serveur/reparchives 000000010000000000000010.00000020.backup
 
Rapporter les bogues à <%s>.
   %s [OPTION]... EMPLACEMENTARCHIVE PLUSANCIENFICHIERWALCONSERVÉ
   -?, --help     affiche cette aide et quitte
   -V, --version  affiche la version et quitte
   -d             affiche des informations de débugage (mode verbeux)
   -n             test, affiche le nom des fichiers qui seraient supprimés
   -x EXT         nettoie les fichiers s'ils ont cette extension
 Page d'accueil de %s : <%s>
 %s supprime les anciens fichiers WAL des archives de PostgreSQL.

 Essayez « %s --help » pour plus d'informations.
 Usage :
 l'emplacement d'archivage « %s » n'existe pas n'a pas pu fermer l'emplacement de l'archive « %s » : %m n'a pas pu ouvrir l'emplacement de l'archive « %s » : %m n'a pas pu lire l'emplacement de l'archive « %s » : %m n'a pas pu supprimer le fichier « %s » : %m erreur :  fatal :  argument du nom de fichier invalide doit spécifier l'emplacement de l'archive doit spécifier le plus ancien journal de transactions conservé trop d'arguments en ligne de commande attention :  