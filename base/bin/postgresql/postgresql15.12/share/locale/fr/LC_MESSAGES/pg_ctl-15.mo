��    �        �   
      �      �     �  &   �     �          "     9     O  /   b     �  "   �  1   �  �     "   �  j   �  o   1     �  D   �  !     3   &  ?   Z  H   �  D   �  C   (  E   l  ?   �  ?   �  >   2  9   q  L   �  B   �  E   ;  �   �  0     F   7  >   ~  B   �  I      %   J  <   p  O   �  7   �     5     <     E     W  M   k     �  -   �  !   �  >     E   X  C   �  y   �  9   \  D   �  C   �  D     E   d  >   �  A   �  '   +  (   S  ,   |  7   �  2   �  6     >   K  *   �  /   �  7   �  4     %   R  %   x  1   �  0   �  #        %  4   C  7   x  2   �  6   �  1     0   L  ,   }  .   �  3   �  7         E   +   e   1   �   6   �   6   �   1   1!  *   c!  "   �!  7   �!  "   �!  $   "  J   1"     |"     �"  3   �"  0   �"     #  #   '#  !   K#     m#  !   �#  $   �#      �#  -   �#     "$  4   B$  %   w$  $   �$  "   �$  !   �$  u   %  F   }%     �%  7   �%  )   &  %   :&  &   `&     �&     �&  /   �&  &   �&  0   '  .   6'  -   e'     �'     �'  &   �'      �'  ,   (  (   1(     Z(  %   u(     �(     �(     �(  M   �(  B   )     ])     n)     �)     �)  #   �)     �)     �)     �)     �)     *      3*  "   T*     w*  �  �*      T,     u,  6   �,  1   �,  ,   �,     #-     C-      _-  5   �-     �-  (   �-  7   �-  �   7.  (   �.  n   /  v   }/  #   �/  H   0  !   a0  :   �0  S   �0  z   1  �   �1  |   2  �   �2  :   3  ?   S3  K   �3  >   �3  �   4  Q   �4  r   �4  �   i5  7   "6  s   Z6  z   �6  G   I7  k   �7  /   �7  G   -8  h   u8  <   �8     9     '9     49     H9  \   e9     �9  3   �9  -   
:  O   8:  \   �:  h   �:  �   N;  I   <  k   ]<  j   �<  i   4=  }   �=  _   >  i   |>  -   �>  6   ?  4   K?  ]   �?  G   �?  >   &@  W   e@  3   �@  /   �@  F   !A  G   hA  7   �A  ;   �A  A   $B  @   fB  3   �B  )   �B  D   C  j   JC  J   �C  U    D  @   VD  C   �D  6   �D  <   E  E   OE  K   �E  *   �E  9   F  B   FF  B   �F  ^   �F  H   +G  -   tG  *   �G  A   �G  8   H  *   HH  i   sH  !   �H  )   �H  ?   )I  H   iI     �I  ,   �I  -   �I  &   &J  6   MJ  0   �J  /   �J  G   �J  &   -K  H   TK  ,   �K  (   �K  !   �K  /   L  �   EL  L   �L  *   "M  ?   MM  +   �M  5   �M  2   �M     "N  '   +N  7   SN  5   �N  9   �N  7   �N  8   3O     lO     �O  4   �O  +   �O  2   �O  1   .P  #   `P  0   �P     �P     �P     �P  q   �P  ]   hQ     �Q     �Q     �Q     R  @   -R     nR     �R     �R  "   �R  /   �R  &   S  $   -S  &   RS     a   2   *   B       �   ,   U   �   )   7       M   �       ;   L   <       R      W          &   b   G   8   4          �   �           -      6      �       O       �      �   T   �           1   K   h   �   �              �   P      {       X          0              �          �   !   :   w   |   �   z       #          p   +       t       V      e           9   N               d       �   @   D   �      �   i   c   %      "   �              r       _   f   s   �   .       �       E   �       /      ]   `      Q   >               Z   �   
           (       v   �           \   m   $   k   �   l       g          A                  =           x       J       n               �   '   ^   [          �       H   q   �   o   �   C   }       y       Y       S      �   ?                	   j   �   �                 u   F   I          ~   3          5    
Allowed signal names for kill:
 
Common options:
 
Options for register and unregister:
 
Options for start or restart:
 
Options for stop or restart:
 
Report bugs to <%s>.
 
Shutdown modes are:
 
Start types are:
   %s init[db]   [-D DATADIR] [-s] [-o OPTIONS]
   %s kill       SIGNALNAME PID
   %s logrotate  [-D DATADIR] [-s]
   %s promote    [-D DATADIR] [-W] [-t SECS] [-s]
   %s register   [-D DATADIR] [-N SERVICENAME] [-U USERNAME] [-P PASSWORD]
                    [-S START-TYPE] [-e SOURCE] [-W] [-t SECS] [-s] [-o OPTIONS]
   %s reload     [-D DATADIR] [-s]
   %s restart    [-D DATADIR] [-m SHUTDOWN-MODE] [-W] [-t SECS] [-s]
                    [-o OPTIONS] [-c]
   %s start      [-D DATADIR] [-l FILENAME] [-W] [-t SECS] [-s]
                    [-o OPTIONS] [-p PATH] [-c]
   %s status     [-D DATADIR]
   %s stop       [-D DATADIR] [-m SHUTDOWN-MODE] [-W] [-t SECS] [-s]
   %s unregister [-N SERVICENAME]
   -?, --help             show this help, then exit
   -D, --pgdata=DATADIR   location of the database storage area
   -N SERVICENAME  service name with which to register PostgreSQL server
   -P PASSWORD     password of account to register PostgreSQL server
   -S START-TYPE   service start type to register PostgreSQL server
   -U USERNAME     user name of account to register PostgreSQL server
   -V, --version          output version information, then exit
   -W, --no-wait          do not wait until operation completes
   -c, --core-files       allow postgres to produce core files
   -c, --core-files       not applicable on this platform
   -e SOURCE              event source for logging when running as a service
   -l, --log=FILENAME     write (or append) server log to FILENAME
   -m, --mode=MODE        MODE can be "smart", "fast", or "immediate"
   -o, --options=OPTIONS  command line options to pass to postgres
                         (PostgreSQL server executable) or initdb
   -p PATH-TO-POSTGRES    normally not necessary
   -s, --silent           only print errors, no informational messages
   -t, --timeout=SECS     seconds to wait when using -w option
   -w, --wait             wait until operation completes (default)
   auto       start service automatically during system startup (default)
   demand     start service on demand
   fast        quit directly, with proper shutdown (default)
   immediate   quit without complete shutdown; will lead to recovery on restart
   smart       quit after all clients have disconnected
  done
  failed
  stopped waiting
 %s home page: <%s>
 %s is a utility to initialize, start, stop, or control a PostgreSQL server.

 %s() failed: %m %s: -S option not supported on this platform
 %s: PID file "%s" does not exist
 %s: WARNING: cannot create restricted tokens on this platform
 %s: WARNING: could not locate all job object functions in system API
 %s: another server might be running; trying to start server anyway
 %s: cannot be run as root
Please log in (using, e.g., "su") as the (unprivileged) user that will
own the server process.
 %s: cannot promote server; server is not in standby mode
 %s: cannot promote server; single-user server is running (PID: %ld)
 %s: cannot reload server; single-user server is running (PID: %ld)
 %s: cannot restart server; single-user server is running (PID: %ld)
 %s: cannot rotate log file; single-user server is running (PID: %ld)
 %s: cannot set core file size limit; disallowed by hard limit
 %s: cannot stop server; single-user server is running (PID: %ld)
 %s: control file appears to be corrupt
 %s: could not access directory "%s": %s
 %s: could not allocate SIDs: error code %lu
 %s: could not create log rotation signal file "%s": %s
 %s: could not create promote signal file "%s": %s
 %s: could not create restricted token: error code %lu
 %s: could not determine the data directory using command "%s"
 %s: could not find own program executable
 %s: could not find postgres program executable
 %s: could not get LUIDs for privileges: error code %lu
 %s: could not get token information: error code %lu
 %s: could not open PID file "%s": %s
 %s: could not open log file "%s": %s
 %s: could not open process token: error code %lu
 %s: could not open service "%s": error code %lu
 %s: could not open service manager
 %s: could not read file "%s"
 %s: could not register service "%s": error code %lu
 %s: could not remove log rotation signal file "%s": %s
 %s: could not remove promote signal file "%s": %s
 %s: could not send log rotation signal (PID: %ld): %s
 %s: could not send promote signal (PID: %ld): %s
 %s: could not send reload signal (PID: %ld): %s
 %s: could not send signal %d (PID: %ld): %s
 %s: could not send stop signal (PID: %ld): %s
 %s: could not start server
Examine the log output.
 %s: could not start server due to setsid() failure: %s
 %s: could not start server: %s
 %s: could not start server: error code %lu
 %s: could not start service "%s": error code %lu
 %s: could not unregister service "%s": error code %lu
 %s: could not write log rotation signal file "%s": %s
 %s: could not write promote signal file "%s": %s
 %s: database system initialization failed
 %s: directory "%s" does not exist
 %s: directory "%s" is not a database cluster directory
 %s: invalid data in PID file "%s"
 %s: missing arguments for kill mode
 %s: no database directory specified and environment variable PGDATA unset
 %s: no operation specified
 %s: no server running
 %s: old server process (PID: %ld) seems to be gone
 %s: option file "%s" must have exactly one line
 %s: out of memory
 %s: server did not promote in time
 %s: server did not start in time
 %s: server does not shut down
 %s: server is running (PID: %ld)
 %s: service "%s" already registered
 %s: service "%s" not registered
 %s: single-user server is running (PID: %ld)
 %s: the PID file "%s" is empty
 %s: too many command-line arguments (first is "%s")
 %s: unrecognized operation mode "%s"
 %s: unrecognized shutdown mode "%s"
 %s: unrecognized signal name "%s"
 %s: unrecognized start type "%s"
 HINT: The "-m fast" option immediately disconnects sessions rather than
waiting for session-initiated disconnection.
 If the -D option is omitted, the environment variable PGDATA is used.
 Is server running?
 Please terminate the single-user server and try again.
 Server started and accepting connections
 Timed out waiting for server startup
 Try "%s --help" for more information.
 Usage:
 Waiting for server startup...
 cannot duplicate null pointer (internal error)
 child process exited with exit code %d child process exited with unrecognized status %d child process was terminated by exception 0x%X child process was terminated by signal %d: %s command not executable command not found could not change directory to "%s": %m could not find a "%s" to execute could not get current working directory: %s
 could not identify current directory: %m could not read binary "%s" could not read symbolic link "%s": %m invalid binary "%s" out of memory out of memory
 program "%s" is needed by %s but was not found in the same directory as "%s"
 program "%s" was found by "%s" but was not the same version as %s
 server promoted
 server promoting
 server shutting down
 server signaled
 server signaled to rotate log file
 server started
 server starting
 server stopped
 starting server anyway
 trying to start server anyway
 waiting for server to promote... waiting for server to shut down... waiting for server to start... Project-Id-Version: PostgreSQL 15
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
Signaux autorisés pour kill :
 
Options générales :
 
Options d'enregistrement ou de dés-enregistrement :
 
Options pour le démarrage ou le redémarrage :
 
Options pour l'arrêt ou le redémarrage :
 
Rapporter les bogues à <%s>.
 
Les modes d'arrêt sont :
 
Les types de démarrage sont :
   %s init[db]   [-D RÉP_DONNÉES] [-s] [-o OPTIONS]
   %s kill       NOM_SIGNAL PID
   %s logrotate  [-D RÉP_DONNÉES] [-s]
   %s promote    [-D RÉP_DONNÉES] [-W] [-t SECS] [-s]
   %s register [-D RÉP_DONNÉES] [-N NOM_SERVICE] [-U NOM_UTILISATEUR] [-P MOT_DE_PASSE]
                  [-S TYPE_DÉMARRAGE] [-e SOURCE] [-W] [-t SECS] [-s] [-o OPTIONS]
   %s reload     [-D RÉP_DONNÉES] [-s]
   %s restart    [-D RÉP_DONNÉES] [-m MODE_ARRÊT] [-W] [-t SECS] [-s]
                    [-o OPTIONS] [-c]
   %s start      [-D RÉP_DONNÉES] [-l FICHIER] [-W] [-t SECS] [-s]
                    [-o OPTIONS] [-p CHEMIN] [-c]
   %s status     [-D RÉP_DONNÉES]
   %s stop       [-D RÉP_DONNÉES] [-m MODE_ARRÊT] [-W] [-t SECS] [-s]
   %s unregister [-N NOM_SERVICE]
   -?, --help               affiche cette aide puis quitte
   -D, --pgdata=RÉP_DONNÉES emplacement du répertoire des données de l'instance
   -N NOM_SERVICE           nom du service utilisé pour l'enregistrement du
                           serveur PostgreSQL
   -P MOT_DE_PASSE          mot de passe du compte utilisé pour
                           l'enregistrement du serveur PostgreSQL
   -S TYPE_DÉMARRAGE        type de démarrage du service pour enregistrer le
                           serveur PostgreSQL
   -U NOM_UTILISATEUR       nom de l'utilisateur du compte utilisé pour
                           l'enregistrement du serveur PostgreSQL
   -V, --version            affiche la version puis quitte
   -W, --no-wait            n'attend pas la fin de l'opération
   -c, --core-files         autorise postgres à produire des fichiers core
   -c, --core-files         non applicable à cette plateforme
   -e SOURCE                source de l'événement pour la trace lors de
                           l'exécution en tant que service
   -l, --log=FICHIER        écrit (ou ajoute) le journal du serveur dans FICHIER
   -m, --mode=MODE          MODE peut valoir « smart », « fast » ou
                           « immediate »
   -o, --options=OPTIONS    options de la ligne de commande à passer à
                           postgres (exécutable du serveur PostgreSQL)
                           ou à initdb
   -p CHEMIN_POSTGRES       normalement pas nécessaire
   -s, --silent             affiche uniquement les erreurs, aucun message
                           d'informations
   -t, --timeout=SECS       durée en secondes à attendre lors de l'utilisation
                           de l'option -w
   -w, --wait               attend la fin de l'opération (par défaut)
   auto       démarre le service automatiquement lors du démarrage du système
             (par défaut)
   demand     démarre le service à la demande
   fast       quitte directement, et arrête correctement (par défaut)
   immediate  quitte sans arrêt complet ; entraîne une restauration au démarrage
             suivant
   smart      quitte après déconnexion de tous les clients
  effectué
  a échoué
  attente arrêtée
 Page d'accueil de %s : <%s>
 %s est un outil pour initialiser, démarrer, arrêter et contrôler un serveur
PostgreSQL.

 échec de %s() : %m %s : option -S non supportée sur cette plateforme
 %s : le fichier de PID « %s » n'existe pas
 %s : ATTENTION : ne peut pas créer les jetons restreints sur cette plateforme
 %s : ATTENTION : n'a pas pu localiser toutes les fonctions objet de job dans l'API système
 %s : un autre serveur semble en cours d'exécution ; le démarrage du serveur
va toutefois être tenté
 %s : ne peut pas être exécuté en tant qu'utilisateur root
Connectez-vous (par exemple en utilisant « su ») sous l'utilisateur (non
 privilégié) qui sera propriétaire du processus serveur.
 %s : ne peut pas promouvoir le serveur ; le serveur n'est pas en standby
 %s : ne peut pas promouvoir le serveur ; le serveur mono-utilisateur est en
cours d'exécution (PID : %ld)
 %s : ne peut pas recharger le serveur ; le serveur mono-utilisateur est en
cours d'exécution (PID : %ld)
 %s : ne peut pas relancer le serveur ; le serveur mono-utilisateur est en
cours d'exécution (PID : %ld)
 %s : ne peut pas faire une rotation de fichier de traces ; le serveur mono-utilisateur est en
cours d'exécution (PID : %ld)
 %s : n'a pas pu initialiser la taille des fichiers core, ceci est interdit
par une limite dure
 %s : ne peut pas arrêter le serveur ; le serveur mono-utilisateur est en
cours d'exécution (PID : %ld)
 %s : le fichier de contrôle semble corrompu
 %s : n'a pas pu accéder au répertoire « %s » : %s
 %s : n'a pas pu allouer les SID : code d'erreur %lu
 %s : n'a pas pu créer le fichier « %s » de demande de rotation des fichiers de trace : %s
 %s : n'a pas pu créer le fichier « %s » signalant la promotion : %s
 %s : n'a pas pu créer le jeton restreint : code d'erreur %lu
 %s : n'a pas déterminer le répertoire des données en utilisant la commande « %s »
 %s : n'a pas pu trouver l'exécutable du programme
 %s : n'a pas pu trouver l'exécutable postgres
 %s :  n'a pas pu obtenir les LUID pour les droits : code d'erreur %lu
 %s : n'a pas pu obtenir l'information sur le jeton : code d'erreur %lu
 %s : n'a pas pu ouvrir le fichier de PID « %s » : %s
 %s : n'a pas pu ouvrir le journal applicatif « %s » : %s
 %s : n'a pas pu ouvrir le jeton du processus : code d'erreur %lu
 %s :  n'a pas pu ouvrir le service « %s » : code d'erreur %lu
 %s : n'a pas pu ouvrir le gestionnaire de services
 %s : n'a pas pu lire le fichier « %s »
 %s : n'a pas pu enregistrer le service « %s » : code d'erreur %lu
 %s : n'a pas pu supprimer le fichier « %s » signalant la demande de rotation des fichiers de trace : %s
 %s : n'a pas pu supprimer le fichier « %s » signalant la promotion : %s
 %s : n'a pas pu envoyer le signal de rotation des fichiers de trace (PID : %ld) : %s
 %s : n'a pas pu envoyer le signal de promotion (PID : %ld) : %s
 %s : n'a pas pu envoyer le signal de rechargement (PID : %ld) : %s
 %s : n'a pas pu envoyer le signal %d (PID : %ld) : %s
 %s : n'a pas pu envoyer le signal d'arrêt (PID : %ld) : %s
 %s : n'a pas pu démarrer le serveur
Examinez le journal applicatif.
 %s : n'a pas pu démarrer le serveur à cause d'un échec de setsid() : %s
 %s : n'a pas pu démarrer le serveur : %s
 %s : n'a pas pu démarrer le serveur : code d'erreur %lu
 %s : n'a pas pu démarrer le service « %s » : code d'erreur %lu
 %s : n'a pas pu supprimer le service « %s » : code d'erreur %lu
 %s : n'a pas pu écrire le fichier « %s » de demande de rotation des fichiers de trace : %s
 %s : n'a pas pu écrire le fichier « %s » signalant la promotion : %s
 %s : l'initialisation du système a échoué
 %s : le répertoire « %s » n'existe pas
 %s : le répertoire « %s » n'est pas un répertoire d'instance
 %s : données invalides dans le fichier de PID « %s »
 %s : arguments manquant pour le mode kill
 %s : aucun répertoire de bases de données indiqué et variable
d'environnement PGDATA non initialisée
 %s : aucune opération indiquée
 %s : aucun serveur en cours d'exécution
 %s : l'ancien processus serveur (PID : %ld) semble être parti
 %s : le fichier d'options « %s » ne doit comporter qu'une seule ligne
 %s : mémoire épuisée
 %s : le serveur ne s'est pas promu à temps
 %s : le serveur ne s'est pas lancé à temps
 %s : le serveur ne s'est pas arrêté
 %s : le serveur est en cours d'exécution (PID : %ld)
 %s : le service « %s » est déjà enregistré
 %s : le service « %s » n'est pas enregistré
 %s : le serveur mono-utilisateur est en cours d'exécution (PID : %ld)
 %s : le fichier PID « %s » est vide
 %s : trop d'arguments en ligne de commande (le premier étant « %s »)
 %s : mode d'opération « %s » non reconnu
 %s : mode d'arrêt non reconnu « %s »
 %s : signal non reconnu « %s »
 %s : type de redémarrage « %s » non reconnu
 ASTUCE : l'option « -m fast » déconnecte immédiatement les sessions plutôt que
d'attendre la déconnexion des sessions déjà présentes.
 Si l'option -D est omise, la variable d'environnement PGDATA est utilisée.
 Le serveur est-il en cours d'exécution ?
 Merci d'arrêter le serveur mono-utilisateur et de réessayer.
 Serveur lancé et acceptant les connexions
 Dépassement du délai pour le démarrage du serveur
 Essayer « %s --help » pour plus d'informations.
 Usage :
 En attente du démarrage du serveur...
 ne peut pas dupliquer un pointeur nul (erreur interne)
 le processus fils a quitté avec le code de sortie %d le processus fils a quitté avec un statut %d non reconnu le processus fils a été terminé par l'exception 0x%X le processus fils a été terminé par le signal %d : %s commande non exécutable commande introuvable n'a pas pu modifier le répertoire par « %s » : %m n'a pas pu trouver un « %s » à exécuter n'a pas pu obtenir le répertoire de travail : %s
 n'a pas pu identifier le répertoire courant : %m n'a pas pu lire le binaire « %s » n'a pas pu lire le lien symbolique « %s » : %m binaire « %s » invalide mémoire épuisée mémoire épuisée
 le programme « %s » est nécessaire pour %s, mais n'a pas été trouvé dans le même répertoire que « %s »
 le programme « %s » a été trouvé par « %s » mais n'est pas de la même version que %s
 serveur promu
 serveur en cours de promotion
 serveur en cours d'arrêt
 envoi d'un signal au serveur
 envoi d'un signal au serveur pour faire une rotation des traces
 serveur démarré
 serveur en cours de démarrage
 serveur arrêté
 lancement du serveur malgré tout
 tentative de lancement du serveur malgré tout
 en attente du serveur à promouvoir... en attente de l'arrêt du serveur... en attente du démarrage du serveur... 