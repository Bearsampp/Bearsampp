��    b      ,  �   <      H      I     j     �     �     �     �  S   �  H   (	  V   q	  =   �	  A   
  U   H
  Z   �
  K   �
  M   E  I   �  I   �  T   '  T   |     �  <   �  D   )  B   n  <   �  D   �  B   3  A   v  :   �  H   �  8   <  6   u  =   �  M   �  K   8  ;   �  U   �  7     =   N  ;   �  :   �  8     <   <  ,   y  0   �  7   �       <        O     c  +   ~     �     �     �     �  %   �     #     +  V   D  )   �  9   �     �       /   >     n     �     �     �  *   �     �  :   �  ,   .  !   [     }     �  3   �  2   �  ;        ?  :   W  :   �     �     �     �        '   3  /   [     �  %   �     �  .   �  #        0     A  0   P     �  /   �  	   �  �  �  $   u     �     �     �     �     �  Z     J   u  [   �  D     D   a  U   �  Z   �  X   W  R   �  O     T   S  k   �  \         q   C   �   [   �   Q   .!  D   �!  T   �!  Q   "  Q   l"  ;   �"  G   �"  K   B#  G   �#  T   �#  M   +$  e   y$  D   �$  L   $%  D   q%  G   �%  D   �%  D   C&  C   �&  U   �&  3   "'  7   V'  >   �'     �'  O   �'      (  .   7(  9   f(     �(     �(      �(     �(  @   )     B)      N)  \   o)  I   �)  A   *  %   X*  $   ~*  @   �*     �*     +  '   +  8   ;+  H   t+     �+  L   �+  8   ,  *   L,     w,     �,  O   �,  U   �,  T   F-  .   �-  I   �-  C   .     X.  '   v.  $   �.  6   �.  D   �.  R   ?/  ,   �/  C   �/  &   0  Q   *0  A   |0     �0     �0  C   �0  $   *1  C   O1     �1                           8   ^            $   @   1   b      Y       3           W           )          C      [   R               !   X   O   Q              0   D      "   7   .   ;   =   A      /   
   ?   P                 6   N          &   	       2   H             #      -       %   >          '      J       M   ]          T   +   (       G      S   9       `       B           4       U       ,       V   *      :   F   5   I         L      \   _      <          a      K   Z       E    
B-tree index checking options:
 
Connection options:
 
Other options:
 
Report bugs to <%s>.
 
Table checking options:
 
Target options:
       --endblock=BLOCK            check table(s) only up to the given block number
       --exclude-toast-pointers    do NOT follow relation TOAST pointers
       --heapallindexed            check that all heap tuples are found within indexes
       --install-missing           install missing extensions
       --maintenance-db=DBNAME     alternate maintenance database
       --no-dependent-indexes      do NOT expand list of relations to include indexes
       --no-dependent-toast        do NOT expand list of relations to include TOAST tables
       --no-strict-names           do NOT require patterns to match objects
       --on-error-stop             stop checking at end of first corrupt page
       --parent-check              check index parent/child relationships
       --rootdescend               search from root page to refind tuples
       --skip=OPTION               do NOT check "all-frozen" or "all-visible" blocks
       --startblock=BLOCK          begin checking table(s) at the given block number
   %s [OPTION]... [DBNAME]
   -?, --help                      show this help, then exit
   -D, --exclude-database=PATTERN  do NOT check matching database(s)
   -I, --exclude-index=PATTERN     do NOT check matching index(es)
   -P, --progress                  show progress information
   -R, --exclude-relation=PATTERN  do NOT check matching relation(s)
   -S, --exclude-schema=PATTERN    do NOT check matching schema(s)
   -T, --exclude-table=PATTERN     do NOT check matching table(s)
   -U, --username=USERNAME         user name to connect as
   -V, --version                   output version information, then exit
   -W, --password                  force password prompt
   -a, --all                       check all databases
   -d, --database=PATTERN          check matching database(s)
   -e, --echo                      show the commands being sent to the server
   -h, --host=HOSTNAME             database server host or socket directory
   -i, --index=PATTERN             check matching index(es)
   -j, --jobs=NUM                  use this many concurrent connections to the server
   -p, --port=PORT                 database server port
   -r, --relation=PATTERN          check matching relation(s)
   -s, --schema=PATTERN            check matching schema(s)
   -t, --table=PATTERN             check matching table(s)
   -v, --verbose                   write a lot of output
   -w, --no-password               never prompt for password
 %*s/%s relations (%d%%), %*s/%s pages (%d%%) %*s/%s relations (%d%%), %*s/%s pages (%d%%) %*s %*s/%s relations (%d%%), %*s/%s pages (%d%%) (%s%-*.*s) %s %s checks objects in a PostgreSQL database for corruption.

 %s home page: <%s>
 %s must be in range %d..%d Are %s's and amcheck's versions compatible? Cancel request sent
 Command was: %s Could not send cancel request:  Query was: %s Try "%s --help" for more information. Usage:
 btree index "%s.%s.%s":
 btree index "%s.%s.%s": btree checking function returned unexpected number of rows: %d cannot specify a database name with --all cannot specify both a database name and database patterns checking btree index "%s.%s.%s" checking heap table "%s.%s.%s" could not connect to database %s: out of memory database "%s": %s detail:  end block out of bounds end block precedes start block error sending command to database "%s": %s error:  heap table "%s.%s.%s", block %s, offset %s, attribute %s:
 heap table "%s.%s.%s", block %s, offset %s:
 heap table "%s.%s.%s", block %s:
 heap table "%s.%s.%s":
 hint:  improper qualified name (too many dotted names): %s improper relation name (too many dotted names): %s in database "%s": using amcheck version "%s" in schema "%s" including database "%s" internal error: received unexpected database pattern_id %d internal error: received unexpected relation pattern_id %d invalid argument for option %s invalid end block invalid start block invalid value "%s" for option %s no btree indexes to check matching "%s" no connectable databases to check matching "%s" no databases to check no heap tables to check matching "%s" no relations to check no relations to check in schemas matching "%s" no relations to check matching "%s" query failed: %s query was: %s
 skipping database "%s": amcheck is not installed start block out of bounds too many command-line arguments (first is "%s") warning:  Project-Id-Version: pg_amcheck (PostgreSQL) 15
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2023-04-12 00:50+0000
PO-Revision-Date: 2023-05-05 17:21+0200
Last-Translator: Ioseph Kim <ioseph@uri.sarang.net>
Language-Team: Korean <kr@postgresql.org>
Language: ko
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=1; plural=0;
 
B-tree 인덱스 검사 옵션들:
 
연결 옵션들:
 
기타 옵션:
 
문제점 보고 주소: <%s>
 
테이블 검사 옵션들:
 
사용가능한 옵션들:
       —endblock=BLOCK            지정된 블록 번호까지 테이블 검사 마침 
       —exclude-toast-pointers    TOAST 포인터를 확인하지 않음
       —heapallindexed            모든 heap 튜플이 인덱스 내에 있는지 검사
       —install-missing           누락된 익스텐션을 설치
       —maintenance-db=DBNAME     대체 연결 데이터베이스
       —no-dependent-indexes      릴레이션에 인덱스를 포함하지 않음 
       —no-dependent-toast        릴레이션에 TOAST 테이블을 포함하지 않음
       —no-strict-names           개체가 패턴과 일치하지 않아도 허용함
       —on-error-stop             손상된 페이지 끝에서 검사를 멈춤
       —parent-check              인덱스의 부모/자식 관계를 검사
       —rootdescend               루트 페이지 부터 튜플을 다시 찾음 
       —skip=OPTION               “all-frozen” 또는 “all-visible” 블록을 검사하지 않음
       —startblock=BLOCK          지정된 블록 번호부터 테이블 검사를 시작
   %s [옵션]... [DB이름]
   -?, --help                      도움말을 표시하고 종료
   -D, —exclude-database=PATTERN  일치하는 데이터베이스를 제외 하고 검사
   -I, —exclude-index=PATTERN     일치하는 인덱스를 제외하고 검사
  -P, —progress                  진행 사항 정보를 보여줌
   -R, —exclude-relation=PATTERN  일치하는 릴레이션을 제외하고 검사
   -S, —exclude-schema=PATTERN    일치하는 스키마를 제외하고 검사
   -T, —exclude-table=PATTERN     일치하는 테이블을 제외하고 검사
   -U, —username=USERNAME         연결할 유저 이름
   -V, --version                   버전 정보를 보여주고 마침
   -W, —password                  암호 입력 프롬프트가 나타남
   -a, —all                       모든 데이터베이스를 검사
   -d, —database=PATTERN          일치하는 모든 데이터베이스를 검사
   -e, --echo                      서버로 보내는 명령들을 보여줌
   -h, —host=HOSTNAME             데이터베이스 서버 호스트 또는 소켓의 디렉터리
   -i, —index=PATTERN             일치하는 인덱스를 검사
   -j, —jobs=NUM                  서버에 동시 연결할 수를 지정
   -p, —port=PORT                 데이터베이스 서버 포트
   -r, —relation=PATTERN          일치하는 릴레이션을 검사
   -s, —schema=PATTERN            일치하는 스키마를 검사
   -t, —table=PATTERN             일치하는 테이블을 검사
   -v, --verbose                   작업내역의 자세한 출력
   -w, —no-password               암호 입력 프롬프트가 나타나지 않음
 %*s/%s 릴레이션 (%d%%), %*s/%s 페이지 (%d%%) %*s/%s 릴레이션 (%d%%), %*s/%s 페이지 (%d%%) %*s %*s/%s 릴레이션 (%d%%), %*s/%s 페이지 (%d%%) (%s%-*.*s) %s %s 가 PostgreSQL 데이터베이스 개체 손상 여부를 검사합니다.

 %s 홈페이지: <%s>
 %s 값은 %d부터 %d까지만 허용합니다 %s 버전과 amcheck의 버전이 호환 가능합니까? 취소 요청 보냄
 사용한 명령: %s 취소 요청 보내기 실패:  사용한 쿼리: %s 자세한 사항은 "%s --help" 명령으로 살펴보십시오. 사용법:
 btree 인덱스 “%s.%s.%s”:
 btree 인덱스 “%s.%s.%s”: btree 확인 중에 예기치 않은 행수를 반환함: %d 데이터베이스 이름을 —all 와 같이 지정할 수 없습니다 데이터베이스 이름과 형식을 지정할 수 없습니다 btree 인덱스 확인 “%s.%s.%s” heap 테이블 확인 “%s.%s.%s” %s 데이터베이스에 연결 할 수 없음: 메모리 부족 데이터베이스 “%s”: %s 상세정보:  마지막 블록이 범위를 벗어남 마지막 블록이 시작 블록보다 앞에 존재함 데이터베이스에 명령을 보내는 중 오류 발생 “%s”: %s 오류:  heap 테이블 “%s.%s.%s”, 블록 %s, 오프셋 %s, 에트리뷰트 %s:
 heap 테이블 “%s.%s.%s”, 블록 %s, 오프셋 %s:
 heap 테이블 “%s.%s.%s”, 블록 %s:
 heap 테이블 “%s.%s.%s”:
 힌트:  바르지 못한 규정 이름(점으로 구분된 이름이 너무 많음): %s 바르지 못한 릴레이션 이름(점으로 구분된 이름이 너무 많음): %s 데이터베이스 “%s”: 사용하는 amcheck 버전 “%s” 스키마 “%s” “%s” 데이터베이스를 포함합니다 내부 오류: 올바르지 않은 데이터베이스 패턴 아이디 %d 내부 오류: 올바르지 않은 릴레이션 패턴 아이디 %d %s 옵션의 잘못된 인자 마지막 블록이 유효하지 않음 시작 블록이 유효하지 않음 "%s" 값은 %s 옵션의 값으로 적당하지 않음 “%s” 와 일치하는 btree 인덱스를 찾을 수 없습니다 “%s” 와 일치하는 연결 가능한 데이터베이스를 찾을 수 없음 확인할 데이터베이스가 없습니다 “%s” 와 일치하는 heap 테이블을 찾을 수 없습니다 확인할 릴레이션이 없습니다 스키마에서 “%s” 와 일치하는 릴레이션을 찾을 수 없습니다 “%s” 와 일치하는 릴레이션을 찾을 수 없습니다 쿼리 실패: %s 사용한 쿼리: %s
 데이터베이스 생략 “%s”: amcheck 가 설치되지 않음 시작 블록이 범위를 벗어남 너무 많은 명령행 인자를 지정했습니다. (처음 "%s") 경고:  