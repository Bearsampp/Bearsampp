��    �      <  �   \      (  ~   )     �  0   �  +   �  q        �  4   �  7   �  s     .   �  G   �  4     )   ;  w   e  4   �  9        L     _  @   s  7   �  ,   �  !        ;  ,   Y  1   �  *   �  -   �  1     '   C  &   k  +   �  "   �  #   �       )     =   7  	   u       &   �  <   �  !   �  	   !  -   +  <   Y  +   �  "   �     �  ,         -  3   L  <   �  *   �  "   �  '        3     G  !   b     �  !   �     �      �  3   �  /   )  '   Y  )   �  *   �  5   �  I     ,   V  /   �  *   �  =   �  '        D     _     {  "   �     �     �  -   �  ,     ,   <  5   i     �  )   �  ?   �  8   %  �   ^     �       0   -  5   ^     �  A   �  L   �  +   8     d  6   r  '   �  #   �     �  (     4   +  )   `  #   �     �     �  /   �           :      S  $   t     �  "   �  ,   �            !   9  '   [     �     �  D   �  +      ?   .   0   n      �   8   �      �      !  &   3!      Z!  �  {!  �   9#     �#  >   $  .   O$  �   ~$  "   	%  F   ,%  .   s%  �   �%  ?   $&  g   d&  C   �&  2   '  �   C'  C   �'  T    (     u(     �(  M   �(  <   �(  >   9)  0   x)  (   �)  =   �)  <   *  5   M*  8   �*  <   �*  2   �*  1   ,+  6   ^+  #   �+  )   �+  	   �+  1   �+  Q   ,  
   q,     |,  2   �,  ]   �,  $   *-     O-  <   \-  D   �-  @   �-  3   .      S.  1   t.  &   �.  6   �.  ?   /  ;   D/  A   �/  7   �/     �/  #   0  .   <0  !   k0  *   �0     �0     �0  A   �0  ;   01  -   l1  3   �1  /   �1  C   �1  W   B2  3   �2  8   �2  6   3  P   >3  ,   �3     �3     �3     �3     4     34     N4  5   m4  4   �4  4   �4  E   5     S5  6   k5  Z   �5  J   �5  �   H6  "   �6  !   7  Q   ;7  9   �7     �7  \   �7  n   =8  ;   �8     �8  =   �8  N   99  ,   �9     �9  3   �9  A   �9  -   =:  2   k:  +   �:  #   �:  ?   �:  &   .;     U;  1   o;  1   �;     �;  '   �;  9   <     P<  "   l<  .   �<  -   �<  )   �<  &   =  i   ==  8   �=  M   �=  8   .>  *   g>  J   �>  )   �>  )   ?  /   1?  (   a?           s   r      A          N   &   Y      W   '         G   <   o   }       u       3       S   T   X   x       h   1                    f          {          ;   5               D       a   ?   %       P   m   *      F   C                       
      n   !   \   �   +   (               E   l      q   2   7              i               �   e   H           :               M   O   [      $             B   8   R   6   w   z   d       ^   )   ~   #   K   t   Z           	   �       -   J   0   k          j   >       �   I   "   9      4              p      @       g   V       |      ]   ,       `   y   /           c   �   _   =   Q           .            U   b         v   L    
If no output file is specified, the name is formed by adding .c to the
input file name, after stripping off .pgc if present.
 
Report bugs to <%s>.
   --regression   run in regression testing mode
   -?, --help     show this help, then exit
   -C MODE        set compatibility mode; MODE can be one of
                 "INFORMIX", "INFORMIX_SE", "ORACLE"
   -D SYMBOL      define SYMBOL
   -I DIRECTORY   search DIRECTORY for include files
   -V, --version  output version information, then exit
   -c             automatically generate C code from embedded SQL code;
                 this affects EXEC SQL TYPE
   -d             generate parser debug output
   -h             parse a header file, this option includes option "-c"
   -i             parse system include files as well
   -o OUTFILE     write result to OUTFILE
   -r OPTION      specify run-time behavior; OPTION can be:
                 "no_indicator", "prepare", "questionmarks"
   -t             turn on autocommit of transactions
 "database" cannot be used as cursor name in INFORMIX mode %s at or near "%s" %s home page: <%s>
 %s is the PostgreSQL embedded SQL preprocessor for C programs.

 %s, the PostgreSQL embedded C preprocessor, version %s
 %s: could not locate my own executable path
 %s: could not open file "%s": %s
 %s: no input files specified
 %s: parser debug support (-d) not available
 AT option not allowed in CLOSE DATABASE statement AT option not allowed in CONNECT statement AT option not allowed in DISCONNECT statement AT option not allowed in SET CONNECTION statement AT option not allowed in TYPE statement AT option not allowed in VAR statement AT option not allowed in WHENEVER statement COPY FROM STDIN is not implemented CREATE TABLE AS cannot specify INTO ERROR:  EXEC SQL INCLUDE ... search starts here:
 Error: include path "%s/%s" is too long on line %d, skipping
 Options:
 SHOW ALL is not implemented Try "%s --help" for more information.
 Unix-domain sockets only work on "localhost" but not on "%s" Usage:
  %s [OPTION]... FILE...

 WARNING:  arrays of indicators are not allowed on input connection %s is overwritten with %s by DECLARE statement %s could not open include file "%s" on line %d could not remove output file "%s"
 cursor "%s" does not exist cursor "%s" has been declared but not opened cursor "%s" is already defined descriptor %s bound to connection %s does not exist descriptor %s bound to the default connection does not exist descriptor header item "%d" does not exist descriptor item "%s" cannot be set descriptor item "%s" is not implemented end of search list
 expected "://", found "%s" expected "@" or "://", found "%s" expected "@", found "%s" expected "postgresql", found "%s" incomplete statement incorrectly formed variable "%s" indicator for array/pointer has to be array/pointer indicator for simple data type has to be simple indicator for struct has to be a struct indicator struct "%s" has too few members indicator struct "%s" has too many members indicator variable "%s" is hidden by a local variable indicator variable "%s" is hidden by a local variable of a different type indicator variable must have an integer type initializer not allowed in EXEC SQL VAR command initializer not allowed in type definition internal error: unreachable state; please report this to <%s> interval specification not allowed here invalid bit string literal invalid connection type: %s invalid data type invalid hexadecimal string literal key_member is always 0 missing "EXEC SQL ENDIF;" missing identifier in EXEC SQL DEFINE command missing identifier in EXEC SQL IFDEF command missing identifier in EXEC SQL UNDEF command missing matching "EXEC SQL IFDEF" / "EXEC SQL IFNDEF" more than one EXEC SQL ELSE multidimensional arrays are not supported multidimensional arrays for simple data types are not supported multidimensional arrays for structures are not supported multilevel pointers (more than 2 levels) are not supported; found %d level multilevel pointers (more than 2 levels) are not supported; found %d levels name "%s" is already declared nested /* ... */ comments nested arrays are not supported (except strings) no longer supported LIMIT #,# syntax passed to server nullable is always 1 only data types numeric and decimal have precision/scale argument only protocols "tcp" and "unix" and database type "postgresql" are supported operator not allowed in variable definition out of memory pointer to pointer is not supported for this data type pointers to varchar are not implemented subquery in FROM must have an alias syntax error syntax error in EXEC SQL INCLUDE command too many levels in nested structure/union definition too many nested EXEC SQL IFDEF conditions trailing junk after numeric literal trailing junk after parameter type "%s" is already defined type name "string" is reserved in Informix mode unhandled previous state in xqs
 unmatched EXEC SQL ENDIF unrecognized data type name "%s" unrecognized descriptor item code %d unrecognized token "%s" unrecognized variable type code %d unsupported feature will be passed to server unterminated /* comment unterminated bit string literal unterminated dollar-quoted string unterminated hexadecimal string literal unterminated quoted identifier unterminated quoted string using variable "%s" in different declare statements is not supported variable "%s" is hidden by a local variable variable "%s" is hidden by a local variable of a different type variable "%s" is neither a structure nor a union variable "%s" is not a pointer variable "%s" is not a pointer to a structure or a union variable "%s" is not an array variable "%s" is not declared variable "%s" must have a numeric type zero-length delimited identifier Project-Id-Version: PostgreSQL 15
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
Si aucun nom de fichier en sortie n'est fourni, le nom est formaté en ajoutant
le suffixe .c au nom du fichier en entrée après avoir supprimé le suffixe .pgc
s'il est présent.
 
Rapporter les bogues à <%s>.
   --regression   s'exécute en mode de tests des régressions
   -?, --help     affiche cette aide et quitte
   -C MODE        configure le mode de compatibilité ; MODE peut être
                 « INFORMIX », « INFORMIX_SE » ou « ORACLE »
   -D SYMBOLE     définit SYMBOLE
   -I RÉPERTOIRE  recherche les fichiers d'en-têtes dans RÉPERTOIRE
   -V, --version  affiche la version et quitte
   -c             produit automatiquement le code C à partir du code SQL
                 embarqué ; ceci affecte EXEC SQL TYPE
   -d             produit la sortie de débogage de l'analyseur
   -h             analyse un fichier d'en-tête, cette option inclut l'option
                 « -c »
   -i             analyse en plus les fichiers d'en-tête systèmes
   -o FICHIER     écrit le résultat dans FICHIER
   -r OPTION      indique le comportement à l'exécution ; OPTION peut valoir :
                 « no_indicator », « prepare », « questionmarks »
   -t             active la validation automatique des transactions
 « database » ne peut pas être utilisé comme nom de curseur dans le mode INFORMIX %s sur ou près de « %s » Page d'accueil de %s : <%s>
 %s est le préprocesseur SQL embarqué de PostgreSQL pour les programmes C.

 %s, le préprocesseur C embarqué de PostgreSQL, version %s
 %s : n'a pas pu localiser le chemin de mon propre exécutable
 %s : n'a pas pu ouvrir le fichier « %s » : %s
 %s : aucun fichier précisé en entrée
 %s : support de débogage de l'analyseur (-d) non disponible
 option AT non autorisée dans une instruction CLOSE DATABASE option AT non autorisée dans une instruction CONNECT option AT non autorisée dans une instruction DISCONNECT option AT non autorisée dans une instruction SET CONNECTION option AT non autorisée dans une instruction TYPE option AT non autorisée dans une instruction VAR option AT non autorisée dans une instruction WHENEVER COPY FROM STDIN n'est pas implanté CREATE TABLE AS ne peut pas indiquer INTO ERREUR :  la recherche EXEC SQL INCLUDE ... commence ici :
 Erreur : le chemin d'en-tête « %s/%s » est trop long sur la ligne %d,
ignoré
 Options :
 SHOW ALL n'est pas implanté Essayer « %s --help » pour plus d'informations.
 les sockets de domaine Unix fonctionnent seulement sur « localhost », mais pas sur « %s » Usage:
  %s [OPTION]... FICHIER...

 ATTENTION :  les tableaux d'indicateurs ne sont pas autorisés en entrée la connexion %s est surchargée avec %s par l'instruction DECLARE %s n'a pas pu ouvrir le fichier d'en-tête « %s » sur la ligne %d n'a pas pu supprimer le fichier « %s » en sortie
 le curseur « %s » n'existe pas le curseur « %s » est déclaré mais non ouvert le curseur « %s » est déjà défini le descripteur %s lié à la connexion %s n'existe pas le descripteur %s lié à la connexion par défaut n'existe pas l'élément d'en-tête du descripteur « %d » n'existe pas l'élément du descripteur « %s » ne peut pas être initialisé l'élément du descripteur « %s » n'est pas implanté fin de la liste de recherche
 « :// » attendu, « %s » trouvé « @ » ou « :// » attendu, « %s » trouvé « @ » attendu, « %s » trouvé « postgresql » attendu, « %s » trouvé instruction incomplète variable « %s » mal formée l'indicateur pour le tableau/pointeur doit être tableau/pointeur l'indicateur d'un type de données simple doit être simple l'indicateur d'un struct doit être un struct le struct indicateur « %s » a trop peu de membres le struct indicateur « %s » a trop de membres la variable indicateur « %s » est cachée par une variable locale la variable indicateur « %s » est caché par une variable locale d'un type
différent la variable d'indicateur doit avoir un type integer initialiseur non autorisé dans la commande EXEC SQL VAR initialiseur non autorisé dans la définition du type erreur interne : l'état ne peut être atteint ; merci de rapporter ceci à <%s> interval de spécification non autorisé ici chaîne bit litéral invalide type de connexion invalide : %s type de données invalide chaîne hexadécimale invalide key_member vaut toujours 0 « EXEC SQL ENDIF; » manquant identifiant manquant dans la commande EXEC SQL DEFINE identifiant manquant dans la commande EXEC SQL IFDEF identifiant manquant dans la commande EXEC SQL UNDEF correspondance manquante « EXEC SQL IFDEF » / « EXEC SQL IFNDEF » plusieurs EXEC SQL ELSE les tableaux multidimensionnels ne sont pas supportés les tableaux multi-dimensionnels pour les types de données simples ne sont
pas supportés les tableaux multidimensionnels ne sont pas supportés pour les structures les pointeurs multi-niveaux (plus de deux) ne sont pas supportés :
%d niveau trouvé les pointeurs multi-niveaux (plus de deux) ne sont pas supportés :
%d niveaux trouvés le nom « %s » est déjà défini commentaires /* ... */ imbriqués les tableaux imbriqués ne sont pas supportés (sauf les chaînes de
caractères) la syntaxe obsolète LIMIT #,# a été passée au serveur nullable vaut toujours 1 seuls les types de données numeric et decimal ont des arguments de
précision et d'échelle seuls les protocoles « tcp » et « unix » et les types de base de données
« postgresql » sont supportés opérateur non autorisé dans la définition de la variable mémoire épuisée ce type de données ne supporte pas les pointeurs de pointeur les pointeurs sur des chaînes de caractères (varchar) ne sont pas implantés la sous-requête du FROM doit avoir un alias erreur de syntaxe erreur de syntaxe dans la commande EXEC SQL INCLUDE trop de niveaux dans la définition de structure/union imbriquée trop de conditions EXEC SQL IFDEF imbriquées élément indésirable après la valeur numérique élément indésirable après le paramètre le type « %s » est déjà défini le nom du type « string » est réservé dans le mode Informix état précédent non géré dans xqs
 EXEC SQL ENDIF différent nom « %s » non reconnu pour un type de données code %d de l'élément du descripteur non reconnu jeton « %s » non reconnu code %d du type de variable non reconnu la fonctionnalité non supportée sera passée au serveur commentaire /* non terminé chaîne bit litéral non terminée chaîne entre guillemets dollars non terminée chaîne hexadécimale litérale non terminée identifiant entre guillemets non terminé chaîne entre guillemets non terminée l'utilisation de la variable « %s » dans différentes instructions de déclaration
n'est pas supportée la variable « %s » est cachée par une variable locale la variable « %s » est cachée par une variable locale d'un type différent la variable « %s » n'est ni une structure ni une union la variable « %s » n'est pas un pointeur la variable « %s » n'est pas un pointeur vers une structure ou une union la variable « %s » n'est pas un tableau la variable « %s » n'est pas déclarée la variable « %s » doit avoir un type numeric identifiant délimité de longueur nulle 