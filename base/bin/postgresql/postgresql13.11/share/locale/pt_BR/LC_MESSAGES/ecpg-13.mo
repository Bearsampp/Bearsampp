��            �         �
  ~   �
     @  0   W  +   �  q   �     &  4   F  7   {  s   �  .   '  G   V  4   �  )   �  w   �  4   u     �     �  @   �  7     ,   J  !   w     �  ,   �  1   �  *     -   A  1   o  '   �  &   �  +   �  "     #   ?     c  )   k  =   �  	   �     �  &   �  <      !   ]  	     -   �  +   �  "   �       ,   !     N     m  *   �  "   �  '   �            !   1     S  !   l     �      �  3   �  /   �  '   (  )   P  *   z  5   �  I   �  ,   %  /   R  *   �  =   �  '   �          .     J     \     w     �  -   �  ,   �  ,     5   0     f  )   �  ?   �  8   �  �   %     �  0   �  5        =  A   R  L   �  +   �       6     '   R  #   z     �  (   �  4   �  )   	     3  /   P      �     �      �  $   �        "     ,   ;     h     �  !   �  '   �     �     	  $   $  D   I  +   �  ?   �  0   �     +  8   J     �     �  &   �      �  �     �   �!     M"  8   d"  -   �"  z   �"  "   F#  ;   i#  ?   �#  ~   �#  :   d$  V   �$  C   �$  ,   :%  �   g%  >   �%     9&     R&  J   k&  >   �&  :   �&  &   0'  /   W'  D   �'  6   �'  /   (  2   3(  6   f(  ,   �(  +   �(  0   �(  '   ')  *   O)     z)  +   �)  I   �)  
   �)      *  7   #*  K   [*  #   �*     �*  7   �*  6   +  *   B+     m+  -   �+     �+     �+  0   �+  -   ,  .   L,     {,     �,  &   �,     �,  &   �,     !-  '   4-  9   \-  8   �-  (   �-  (   �-  (   !.  C   J.  X   �.  /   �.  8   /  6   P/  ?   �/  3   �/     �/     0     30      J0     k0     �0  0   �0  0   �0  0   �0  <   01     m1  /   �1  J   �1  ?   2  �   A2      3  E   ,3  ?   r3     �3  Q   �3  W   4  6   q4     �4  ?   �4  .   �4  &   $5     K5  +   [5  :   �5  +   �5     �5  3   6  $   ?6  &   d6  '   �6  /   �6     �6  /   7  7   27  !   j7  !   �7  6   �7  3   �7  ,   8  3   F8  1   z8  P   �8  6   �8  K   49  7   �9  "   �9  C   �9  !   :  !   A:  )   c:  )   �:         L   +          D   ^       >   w       !   P       o   N   p         z                &            c          O   4   }       e   "           <   `   W       X      ;   {       (       r   0   6   _   S   .   I   g       V              \   ,              G   k   	   ]       d   f   n   8   1          m   #       )   2         U   '   q      b          E          =   3       %   u       K      $   R   l       T       C      h              y   
   5          [           a   :   A   M       s      B       H   *              J   v   7                     Y      i   j   F   /   -       ?          t   9   Z       @       |       ~               x   Q    
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
 %s at or near "%s" %s home page: <%s>
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

 WARNING:  arrays of indicators are not allowed on input could not open include file "%s" on line %d could not remove output file "%s"
 cursor "%s" does not exist cursor "%s" has been declared but not opened cursor "%s" is already defined descriptor "%s" does not exist descriptor header item "%d" does not exist descriptor item "%s" cannot be set descriptor item "%s" is not implemented end of search list
 expected "://", found "%s" expected "@" or "://", found "%s" expected "@", found "%s" expected "postgresql", found "%s" incomplete statement incorrectly formed variable "%s" indicator for array/pointer has to be array/pointer indicator for simple data type has to be simple indicator for struct has to be a struct indicator struct "%s" has too few members indicator struct "%s" has too many members indicator variable "%s" is hidden by a local variable indicator variable "%s" is hidden by a local variable of a different type indicator variable must have an integer type initializer not allowed in EXEC SQL VAR command initializer not allowed in type definition internal error: unreachable state; please report this to <%s> interval specification not allowed here invalid bit string literal invalid connection type: %s invalid data type invalid hex string literal key_member is always 0 missing "EXEC SQL ENDIF;" missing identifier in EXEC SQL DEFINE command missing identifier in EXEC SQL IFDEF command missing identifier in EXEC SQL UNDEF command missing matching "EXEC SQL IFDEF" / "EXEC SQL IFNDEF" more than one EXEC SQL ELSE multidimensional arrays are not supported multidimensional arrays for simple data types are not supported multidimensional arrays for structures are not supported multilevel pointers (more than 2 levels) are not supported; found %d level multilevel pointers (more than 2 levels) are not supported; found %d levels nested /* ... */ comments nested arrays are not supported (except strings) no longer supported LIMIT #,# syntax passed to server nullable is always 1 only data types numeric and decimal have precision/scale argument only protocols "tcp" and "unix" and database type "postgresql" are supported operator not allowed in variable definition out of memory pointer to pointer is not supported for this data type pointers to varchar are not implemented subquery in FROM must have an alias syntax error syntax error in EXEC SQL INCLUDE command too many levels in nested structure/union definition too many nested EXEC SQL IFDEF conditions type "%s" is already defined type name "string" is reserved in Informix mode unhandled previous state in xqs
 unmatched EXEC SQL ENDIF unrecognized data type name "%s" unrecognized descriptor item code %d unrecognized token "%s" unrecognized variable type code %d unsupported feature will be passed to server unterminated /* comment unterminated bit string literal unterminated dollar-quoted string unterminated hexadecimal string literal unterminated quoted identifier unterminated quoted string using unsupported DESCRIBE statement using variable "%s" in different declare statements is not supported variable "%s" is hidden by a local variable variable "%s" is hidden by a local variable of a different type variable "%s" is neither a structure nor a union variable "%s" is not a pointer variable "%s" is not a pointer to a structure or a union variable "%s" is not an array variable "%s" is not declared variable "%s" must have a numeric type zero-length delimited identifier Project-Id-Version: PostgreSQL 13
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2021-05-09 10:51-0300
PO-Revision-Date: 2018-06-25 08:59+0200
Last-Translator: Euler Taveira <euler@eulerto.com>
Language-Team: Brazilian Portuguese <pgsql-translators@postgresql.org>
Language: pt_BR
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=2; plural=(n>1);
 
Se nenhum arquivo de saída for especificado, o nome é formado adicionando .c ao
nome do arquivo de entrada, após remover .pgc se presente.
 
Relate erros a <%s>.
   --regression   executa em modo de teste de regressão
   -?, --help     mostra essa ajuda e termina
   -C mode        define o modo de compatilidade; MODE pode ser um de
                 "INFORMIX", "INFORMIX_SE", "ORACLE"
   -D SÍMBOLO     define SÍMBOLO
   -I DIRETÓRIO   procura DIRETÓRIO para incluir arquivos
   -V, --version  mostra informação sobre a versão e termina
   -c             gera automaticamente código C a partir do código SQL embutido;
                 isso afeta o EXEC SQL TYPE
   -d             gera saída de depuração do analisador
   -h             analisa um arquivo de cabeçalho, essa opção inclui a opção "-c"
   -i             analisa arquivos de cabeçalho do sistema também
   -o ARQUIVO     grava resultado no ARQUIVO
   -r OPÇÃO       especifica comportamento em tempo de execução; OPÇÃO pode ser:
                 "no_indicator", "prepare", "questionmarks"
   -t             habilita o auto efetivação de transações
 %s em ou próximo a "%s" página web do %s: <%s>
 %s é o pré-processador SQL embutido do PostgreSQL para programas em C.

 %s, the pré-processador C embutido do PostgreSQL, versão %s
 %s: não pôde localizar meu próprio caminho executável
 %s: não pôde abrir arquivo "%s": %s
 %s: nenhum arquivo de entrada foi especificado
 %s: suporte a depuração do analisador (-d) não está disponível
 opção AT não é permitida no comando CLOSE DATABASE opção AT não é permitida no comando CONNECT opção AT não é permitida no comando DISCONNECT opção AT não é permitida no comando SET CONNECTION opção AT não é permitida no comando TYPE opção AT não é permitida no comando VAR opção AT não é permitida no comando WHENEVER COPY FROM STDIN não está implementado CREATE TABLE AS não pode especificar INTO ERRO:  EXEC SQL INCLUDE ... pesquisa inicia aqui:
 Erro: caminho de inclusão "%s/%s" é muito longo na linha %d, ignorando
 Opções:
 SHOW ALL não está implementado Tente "%s --help" para obter informações adicionais.
 Soquetes de domínio Unix trabalham somente com "localhost" e não com "%s" Uso:
  %s [OPÇÃO]... ARQUIVO...

 AVISO:  matrizes do indicadores não são permitidas na entrada não pôde abrir arquivo de inclusão "%s" na linha %d não pôde remover arquivo de saída "%s"
 cursor "%s" não existe cursor "%s" foi declarado mas não foi aberto cursor "%s" já está definido descritor "%s" não existe item do cabeçalho do descritor "%d" não existe item do descritor "%s" não pode ser definido item do descritor "%s" não está implementado fim da lista de pesquisa
 esperado "://", encontrado "%s" esperado "@" ou "://", encontrado "%s" esperado "@", encontrado "%s" esperado "postgresql", encontrado "%s" comando incompleto variável "%s" possui formato incorreto indicador para matriz/ponteiro tem de ser matriz/ponteiro indicador para tipo de dados simples tem que ser simples indicador para struct tem que ser struct indicador struct "%s" tem poucos membros indicador struct "%s" tem muitos membros variável de indicador "%s" está escondida por uma variável local variável de indicador "%s" está escondida por uma variável local de um tipo diferente variável de indicador deve ter um tipo inteiro inicilização não é permitido no comando EXEC SQL VAR inicializador não é permitido na definição do tipo erro interno: estado inacessível; por favor relato isso a <%s> especificação de intervalo não é permitida aqui cadeia de bits inválida tipo de conexão inválido: %s tipo de dado inválido cadeia de hexadecimais inválida key_member é sempre 0 faltando "EXEC SQL ENDIF;" faltando identificador no comando EXEC SQL IFDEF faltando identificador no comando EXEC SQL IFDEF faltando identificador no comando EXEC SQL UNDEF faltando correspondente "EXEC SQL IFDEF" / "EXEC SQL IFNDEF" mais de um EXEC SQL ELSE matrizes multidimensionais não são suportadas matrizes multidimensionais para tipo de dados simples não são suportadas matrizes multidimensionais para estruturas não são suportadas ponteiros com múltiplos níveis (mais do que 2 níveis) não são suportados; %d nível encontrado ponteiros com múltiplos níveis (mais do que 2 níveis) não são suportados; %d níveis encontrados comentários /* ... */ aninhados matrizes aninhadas não são suportadas (exceto cadeia de caracteres) sintaxe LIMIT #,# que não é suportada foi enviada ao servidor nullable é sempre 1 somente os tipos de dados numeric e decimal possuem argumento de precisão/escala somente os protocolos "tcp" e "unix" e tipo banco de dados "postgressql" sãosuportados operador não é permitido na definição da variável sem memória ponteiro para ponteiro não é suportado para esse tipo de dado ponteiros para varchar não estão implentados subconsulta no FROM deve ter um aliás erro de sintaxe erro de sintaxe no comando EXEC SQL INCLUDE muitos níveis em definição aninhada de estrutura/união muitas condições EXEC SQL IFDEF aninhadas tipo "%s" já está definido nome de tipo "string" é reservado no modo Informix estado anterior não tratado em xqs
 EXEC SQL ENDIF não tem correspondente nome do tipo dados "%s" é desconhecido código do item do descritor %d é desconhecido informação desconhecida "%s" tipo de código de variável %d é desconhecido funcionalidade não suportada será enviada ao servidor comentário /* não foi terminado cadeia de bits não foi terminada cadeia de caracteres entre dólares não foi terminada cadeia de caracteres hexadecimal não foi terminada identificador entre aspas não foi terminado cadeia de caracteres entre aspas não foi terminada utilizando comando DESCRIBE que não é suportado utilizar variável "%s" em comandos de declaração diferentes não é suportado variável "%s" está escondida por uma variável local variável "%s" está escondida por uma variável local de um tipo diferente variável "%s" não é nem uma estrutura nem uma união variável "%s" não é um ponteiro variável "%s" não é um ponteiro para uma estrutura ou uma união variável "%s" não é uma matriz variável "%s" não foi declarada variável "%s" deve ter um tipo númerico identificador delimitado tem tamanho zero 