Þ    |      ü  §   Ü      x
  ~   y
  3   ø
  0   ,  +   ]  q        û  4     7   P  s     .   ü  G   +  4   s  )   ¨  w   Ò  4   J       @     7   Ó  ,     !   8     Z  ,   x  1   ¥  *   ×  -     1   0  '   b  &     +   ±  "   Ý  #         $  )   ,  =   V  	          &   º  <   á  !     	   @  -   J  +   x  "   ¤     Ç  ,   â          .  *   M  "   x  '        Ã     ×  !   ò       !   -     O      d  3     /   ¹  '   é  )     *   ;  5   f  I     ,   æ  /     *   C  Z   n  '   É     ñ          (     :     Q  -   k  ,     ,   Æ  5   ó     )  )   E  ?   o  8   ¯     è       0     5   Ê        A     L   W  +   ¤     Ð  6   Þ  '     #   =     a  (   n  4     )   Ì     ö  /        C      \  $   }     ¢  "   º  ,   Ý     
     "  !   B  '   d          «  $   Æ  D   ë  +   0  ?   \  0        Í  8   ì     %     C  &   a        £  ©     M!  2   ç!  6   "  6   Q"     "     ##  6   C#  3   z#     ®#  0   <$  O   m$  :   ½$  *   ø$  ¥   #%  3   É%     ý%  K   &  0   Y&  :   &  #   Å&  #   é&  8   '  6   F'  /   }'  2   ­'  6   à'  ,   (  +   D(  0   p(  &   ¡(  2   È(     û(  .   )  M   3)     )     )  A   ­)  Y   ï)  $   I*     n*  7   w*  5   ¯*  0   å*     +  2   6+     i+     +  +   ¢+  2   Î+  1   ,     3,  )   I,  4   s,  '   ¨,  0   Ð,     -     -  ;   2-  ;   n-  '   ª-  0   Ò-  0   .  =   4.  Q   r.  2   Ä.  @   ÷.  :   8/  q   s/  1   å/  $   0     <0     X0     s0     0  *   £0  )   Î0  )   ø0  8   "1     [1  '   {1  B   £1  4   æ1  S   2     o2  9   2  F   Ä2     3  Z   *3  T   3  ?   Ú3     4  O   +4  2   {4  J   ®4     ù4  /   5  8   75  0   p5     ¡5  P   Á5  "   6  1   56  /   g6      6  ,   ¸6  1   å6     7  (   27  +   [7  *   7  +   ²7  +   Þ7  '   
8  Q   28  3   8  G   ¸8  (    9  "   )9  5   L9     9  "   ¢9  )   Å9     ï9     j              4      f       g   ?       ^   l   y           
      T   (   9       v          8      R      L   u      X   d   \   %   )           <   i      =   '   Q   N   &                  :   M                    w       ,   7      B   .           {                  ;   S             o   #   >   P       E   "   H   s      ]              [   !   *       t           Z      6      	      G          A       O      -   _       2   r       3       W   x   1   k   +   U   e           Y   p   K   n           q   0   a   I      J   5       |       h      c   m                   C   @   V   F      z   D   /       `   $         b    
If no output file is specified, the name is formed by adding .c to the
input file name, after stripping off .pgc if present.
 
Report bugs to <pgsql-bugs@lists.postgresql.org>.
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
 %s at or near "%s" %s is the PostgreSQL embedded SQL preprocessor for C programs.

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
 expected "://", found "%s" expected "@" or "://", found "%s" expected "@", found "%s" expected "postgresql", found "%s" incomplete statement incorrectly formed variable "%s" indicator for array/pointer has to be array/pointer indicator for simple data type has to be simple indicator for struct has to be a struct indicator struct "%s" has too few members indicator struct "%s" has too many members indicator variable "%s" is hidden by a local variable indicator variable "%s" is hidden by a local variable of a different type indicator variable must have an integer type initializer not allowed in EXEC SQL VAR command initializer not allowed in type definition internal error: unreachable state; please report this to <pgsql-bugs@lists.postgresql.org> interval specification not allowed here invalid bit string literal invalid connection type: %s invalid data type key_member is always 0 missing "EXEC SQL ENDIF;" missing identifier in EXEC SQL DEFINE command missing identifier in EXEC SQL IFDEF command missing identifier in EXEC SQL UNDEF command missing matching "EXEC SQL IFDEF" / "EXEC SQL IFNDEF" more than one EXEC SQL ELSE multidimensional arrays are not supported multidimensional arrays for simple data types are not supported multidimensional arrays for structures are not supported multilevel pointers (more than 2 levels) are not supported; found %d level multilevel pointers (more than 2 levels) are not supported; found %d levels nested /* ... */ comments nested arrays are not supported (except strings) no longer supported LIMIT #,# syntax passed to server nullable is always 1 only data types numeric and decimal have precision/scale argument only protocols "tcp" and "unix" and database type "postgresql" are supported operator not allowed in variable definition out of memory pointer to pointer is not supported for this data type pointers to varchar are not implemented subquery in FROM must have an alias syntax error syntax error in EXEC SQL INCLUDE command too many levels in nested structure/union definition too many nested EXEC SQL IFDEF conditions type "%s" is already defined type name "string" is reserved in Informix mode unmatched EXEC SQL ENDIF unrecognized data type name "%s" unrecognized descriptor item code %d unrecognized token "%s" unrecognized variable type code %d unsupported feature will be passed to server unterminated /* comment unterminated bit string literal unterminated dollar-quoted string unterminated hexadecimal string literal unterminated quoted identifier unterminated quoted string using unsupported DESCRIBE statement using variable "%s" in different declare statements is not supported variable "%s" is hidden by a local variable variable "%s" is hidden by a local variable of a different type variable "%s" is neither a structure nor a union variable "%s" is not a pointer variable "%s" is not a pointer to a structure or a union variable "%s" is not an array variable "%s" is not declared variable "%s" must have a numeric type zero-length delimited identifier Project-Id-Version: ecpg (PostgreSQL) 12
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2020-02-09 20:09+0000
PO-Revision-Date: 2019-11-01 11:02+0900
Last-Translator: Ioseph Kim <ioseph@uri.sarang.net>
Language-Team: Korean Team <pgsql-kr@postgresql.kr>
Language: ko
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=1; plural=0;
 
ì¶ë ¥ íì¼ ì´ë¦ì ì§ì íì§ ìì¼ë©´ ìë ¥ íì¼ ì´ë¦ì .pgcê° ìì ê²½ì° ì ê±°íê³ 
.cë¥¼ ì¶ê°íì¬ ì´ë¦ì´ ì§ì ë©ëë¤.
 
ì¤ë¥ë³´ê³ : <pgsql-bugs@lists.postgresql.org>.
   --regression   íê· íì¤í¸ ëª¨ëìì ì¤í
   -?, --help     ì´ ëìë§ì ë³´ì¬ì£¼ê³  ë§ì¹¨
   -C MODE        í¸íì± ëª¨ëë¥¼ ì¤ì í©ëë¤. MODEë ë¤ì ì¤ íëì¼ ì ììµëë¤.
                 "INFORMIX", "INFORMIX_SE", "ORACLE"
   -D SYMBOL      SYMBOL ì ì
   -I DIRECTORY   DIRECTORYìì í¬í¨ íì¼ ê²ì
   -V, --version  ë²ì  ì ë³´ ë³´ì¬ì£¼ê³  ë§ì¹¨
   -c             í¬í¨ë SQL ì½ëìì ìëì¼ë¡ C ì½ëë¥¼ ìì±í©ëë¤.
                 EXEC SQL TYPEì ìí¥ì ì¤ëë¤.
   -d             íì ëë²ê·¸ ì¶ë ¥ ìì±
   -h             í¤ë íì¼ êµ¬ë¬¸ ë¶ì. ì´ ìµìì "-c" ìµì í¬í¨
   -i             ìì¤í í¬í¨ íì¼ë êµ¬ë¬¸ ë¶ì
   -o OUTFILE     OUTFILEì ê²°ê³¼ ì°ê¸°
   -r OPTION      ë°íì ëìì ì§ì í©ëë¤. ì¬ì© ê°ë¥í OPTIONì ë¤ìê³¼ ê°ìµëë¤.
                 "no_indicator", "prepare", "questionmarks"
   -t             í¸ëì­ì ìë ì»¤ë° ì¤ì 
 %s, "%s" ë¶ê·¼ %sì(ë) C íë¡ê·¸ë¨ì© PostgreSQL í¬í¨ SQL ì ì²ë¦¬ê¸°ìëë¤.

 %s, PostgreSQL í¬í¨ C ì ì²ë¦¬ê¸°, ë²ì  %s
 %s: ì¤í ê°ë¥í ê²½ë¡ë¥¼ ì§ì í  ì ììµëë¤
 %s: "%s" íì¼ ì´ ì ìì: %s
 %s: ì§ì ë ìë ¥ íì¼ ìì
 %s: íì ëë²ê·¸ ì§ì(-d)ì ì¬ì©í  ì ìì
 CLOSE DATABASE ë¬¸ì AT ìµìì´ íì©ëì§ ìì CONNECT ë¬¸ì AT ìµìì´ íì©ëì§ ìì DISCONNECT ë¬¸ì AT ìµìì´ íì©ëì§ ìì SET CONNECTION ë¬¸ì AT ìµìì´ íì©ëì§ ìì TYPE ë¬¸ì AT ìµìì´ íì©ëì§ ìì VAR ë¬¸ì AT ìµìì´ íì©ëì§ ìì WHENEVER ë¬¸ì AT ìµìì´ íì©ëì§ ìì COPY FROM STDINì´ êµ¬íëì§ ìì CREATE TABLE ASìì INTOë¥¼ ì§ì í  ì ìì ì¤ë¥:  EXEC SQL INCLUDE ... ì¬ê¸°ì ê²ì ìì:
 ì¤ë¥: í¬í¨ ê²½ë¡ "%s/%s"ì´(ê°) %dì¤ìì ëë¬´ ê¸¸ì´ì ê±´ëë
 ìµìë¤:
 SHOW ALLì´ êµ¬íëì§ ìì ìì í ì¬í­ì "%s --help" ëªë ¹ì¼ë¡ ì´í´ë³´ì­ìì¤.
 Unix-domain ìì¼ì "localhost"ììë§ ìëíë©° "%s"ììë ìëíì§ ìì ì¬ì©:
  %s [OPTION]... íì¼...

 ê²½ê³ :  ìë ¥ìì íìê¸°ì ë°°ì´ì´ íì©ëì§ ìì í¬í¨ íì¼ "%s"ì(ë¥¼) %dì¤ìì ì´ ì ìì ì¶ë ¥ íì¼ "%s"ì(ë¥¼) ì ê±°í  ì ìì
 "%s" ì´ë¦ì ì»¤ìê° ìì "%s" ì»¤ìê° ì ì¸ëìì§ë§ ì´ë¦¬ì§ ìì "%s" ì»¤ìê° ì´ë¯¸ ì ìë¨ "%s" ì¤ëªìê° ìì ì¤ëªì í¤ë í­ëª© "%d"ì´(ê°) ìì ì¤ëªì í­ëª© "%s"ì(ë¥¼) ì¤ì í  ì ìì ì¤ëªì í­ëª© "%s"ì´(ê°) êµ¬íëì§ ìì ê²ì ëª©ë¡ì ë
 "://"ê° íìíë° "%s"ì´(ê°) ìì "@" ëë "://"ê° íìíë° "%s"ì´(ê°) ìì "@"ì´ íìíë° "%s"ì´(ê°) ìì "postgresql"ì´ íìíë° "%s"ì´(ê°) ìì ë¶ìì í ë¬¸ ìëª»ë íìì ë³ì "%s" ë°°ì´/í¬ì¸í°ì íìê¸°ë ë°°ì´/í¬ì¸í°ì¬ì¼ í¨ ë¨ì ë°ì´í° íìì íìê¸°ë ë¨ìì´ì´ì¼ í¨ êµ¬ì¡°ì íìê¸°ë êµ¬ì¡°ì¬ì¼ í¨ "%s" ì§ì êµ¬ì¡°ì²´ë ë§´ë²ê° ëë¬´ ì ì "%s" ì§ì êµ¬ì¡°ì²´ë ë§´ë²ê° ëë¬´ ë§ì "%s" ì§ìì ë³ìê° ì§ì­ ë³ìì ìí´ ì¨ê²¨ì¡ì "%s" ì§ìì ë³ìê° ì§ì­ ë³ìì ë¤ë¥¸ ìë£í ëë¬¸ì ì¨ê²¨ì¡ì íìê¸° ë³ìì ì ì íìì´ ìì´ì¼ í¨ EXEC SQL VAR ëªë ¹ì ì´ëìë¼ì´ì ê° íì©ëì§ ìì íì ì ìì ì´ëìë¼ì´ì ê° íì©ëì§ ìì ë´ë¶ ì¤ë¥: ì°ê²°í  ì ììµëë¤. ì´ ë¬¸ì ë¥¼ <pgsql-bugs@lists.postgresql.org>ë¡ ìë ¤ì£¼ì­ìì¤. ì¬ê¸°ìë ê°ê²© ì§ì ì´ íì©ëì§ ìì ìëª»ë ë¹í¸ ë¬¸ìì´ ë¦¬í°ë´ ìëª»ë ì°ê²° íì: %s ìëª»ë ë°ì´í° íì key_memberë í­ì 0 "EXEC SQL ENDIF;" ëë½ EXEC SQL DEFINE ëªë ¹ì ìë³ì ëë½ EXEC SQL IFDEF ëªë ¹ì ìë³ì ëë½ EXEC SQL UNDEF ëªë ¹ì ìë³ì ëë½ ì¼ì¹íë "EXEC SQL IFDEF" / "EXEC SQL IFNDEF" ëë½ ë ê° ì´ìì EXEC SQL ELSE ë¤ì°¨ì ë°°ì´ì´ ì§ìëì§ ìì ë¨ì ë°ì´í° íìì ë¤ì°¨ì ë°°ì´ì´ ì§ìëì§ ìì êµ¬ì¡°ìë ë¤ì°¨ì ë°°ì´ì´ ì§ìëì§ ìì ë¤ì¤ë¨ê³ í¬ì¸í°(2ë¨ê³ ì´ì)ë ì§ìíì§ ìì; ë°ê²¬ë ë ë²¨: %d ì¤ì²©ë /* ... */ ì£¼ì ì¤ì²©ë ë°°ì´ì ì§ìëì§ ìì(ë¬¸ìì´ ì ì¸) ë ì´ì ì§ìëì§ ìë LIMIT #,# êµ¬ë¬¸ì´ ìë²ì ì ë¬ë¨ null íì© ì¬ë¶ë í­ì 1 ì«ì ë° 10ì§ì ë°ì´í° íììë§ ì ì²´ ìë¦¿ì/ìì ìë¦¿ì ì¸ì í¬í¨ "tcp" ë° "unix" íë¡í ì½ê³¼ ë°ì´í°ë² ì´ì¤ íì "postgresql"ë§ ì§ìë¨ ì°ì°ìë ëì  ì ì ìì­ììë ì¬ì©í  ì ìì ë©ëª¨ë¦¬ ë¶ì¡± ì´ ë°ì´í° íììë í¬ì¸í°ì ëí í¬ì¸í°ê° ì§ìëì§ ìì varcharì ëí í¬ì¸í°ê° êµ¬íëì§ ìì FROM ì  ë´ì subquery ìë ë°ëì alias ë¥¼ ê°ì ¸ì¼ë§ í©ëë¤ êµ¬ë¬¸ ì¤ë¥ EXEC SQL INCLUDE ëªë ¹ì êµ¬ë¬¸ ì¤ë¥ ë°ì ì¤ì²©ë êµ¬ì¡°/union ì ìì ìì¤ì´ ëë¬´ ë§ì ì¤ì²©ë EXEC SQL IFDEF ì¡°ê±´ì´ ëë¬´ ë§ì "%s" íìì´ ì´ë¯¸ ì ìë¨ "string" ìë£í ì´ë¦ì ì¸í¬ë¯¹ì¤ ëª¨ëìì ìì½ì´ë¡ ì°ìëë¤ ì¼ì¹íì§ ìë EXEC SQL ENDIF ì¸ìí  ì ìë ë°ì´í° íì ì´ë¦ "%s" ì¸ìí  ì ìë ì¤ëªì í­ëª© ì½ë %d ì¸ìí  ì ìë í í° "%s" ì¸ìí  ì ìë ë³ì íì ì½ë %d ì§ìëì§ ìë ê¸°ë¥ì´ ìë²ì ì ë¬ë¨ ë§ë¬´ë¦¬ ìë /* ì£¼ì ë§ë¬´ë¦¬ ìë ë¹í¸ ë¬¸ìì´ ë¬¸ì ë§ë¬´ë¦¬ ìë ë°ì´í ìì ë¬¸ìì´ ë§ë¬´ë¦¬ ìë 16ì§ì ë¬¸ìì´ ë¬¸ì ë§ë¬´ë¦¬ ìë ë°ì´í ìì ìë³ì ë§ë¬´ë¦¬ ìë ë°ì´í ìì ë¬¸ìì´ ì§ìëì§ ìë DESCRIBE ë¬¸ ì¬ì© ìë¡ ë¤ë¥¸ ì ì¸ êµ¬ë¬¸ìì "%s" ë³ì ì¬ì©ì ì§ìíì§ ììµëë¤ "%s" ë³ìê° ì§ì­ ë³ìì ìí´ ì¨ê²¨ì¡ì "%s" ë³ìê° ë¤ë¥¸ ìë£íì ì§ì­ ë³ìì ìí´ ì¨ê²¨ì¡ì "%s" ë³ìê° êµ¬ì¡°ë unionì´ ìë "%s" ë³ìê° í¬ì¸í°ê° ìë "%s" ë³ìê° êµ¬ì¡°ë unionì í¬ì¸í°ê° ìë "%s" ë³ìê° ë°°ì´ì´ ìë "%s" ë³ìê° ì ì¸ëì§ ìì "%s" ë³ìë ì«ì íìì´ì´ì¼ í¨ ê¸¸ì´ê° 0ì¸ êµ¬ë¶ ìë³ì 