��    e      D  �   l      �     �  8   �  D   �  8   &	  4   _	  >   �	  <   �	  I   
  9   Z
  ?   �
  7   �
       /   ,  /   \  1   �     �  3   �  ,     !   3  $   U  $   z     �  $   �  .   �  &     '   8      `  	   �  $   �     �  %   �  d   �  8   R  3   �  #   �  "   �  #        *  $   H  /   m     �     �  "   �     �       (   4  '   ]  *   �  )   �  !   �     �  #        =     U     o  )   �     �  )   �  &   �  %   $     J  ,   S     �     �     �  4   �  6   �     )     E  $   L     q      �     �     �     �               $     1     C     T     r     �     �  L   �  A   �     -  /   H     x     �     �     �     �     �               7  %   I     o  	   �  �  �     !  C   A  C   �  @   �  =   
  E   H  :   �  X   �  9   "  C   \  Q   �  2   �  I   %  J   o  =   �     �  I     5   Y  '   �  2   �  2   �  '     (   E  (   n  @   �  '   �  -         .  /   :     j  =   �  �   �  Z   @  ]   �  0   �  '   *   2   R      �   =   �   7   �   )   !     ;!  *   Y!  $   �!  (   �!  1   �!  9   "  1   >"  5   p"  $   �"  !   �"  /   �"  #   #  &   A#  $   h#  8   �#  .   �#  E   �#  -   ;$  /   i$     �$  >   �$     �$  #   �$  !   %  .   6%  :   e%  !   �%     �%  +   �%  )   �%  +   !&  #   M&  /   q&  #   �&     �&     �&     �&     �&     	'  -   '     K'     \'     n'  b   �'  Y   �'      @(  C   a(  "   �(     �(     �(     )  '    )     H)     c)     �)     �)  +   �)  "   �)     
*     T   9   C      b                     +                &   8          F   Q      (   #   _       =   W   A       \   P   H   -      5       %           !   ^   e   E                               J       G   Y   >   `   $   B   '             V   /           [   N                           X   )   K   .   M   S       I          3       ?   2          7   Z   U              "      L   ,   ]       c   0   R   4   :       	   6       1       a                  
   d   @   D       *       O            <          ;    
Report bugs to <%s>.
   -?, --help                  show this help, then exit
   -V, --version               output version information, then exit
   -e, --exit-on-error         exit immediately on error
   -i, --ignore=RELATIVE_PATH  ignore indicated path
   -m, --manifest-path=PATH    use specified path for manifest
   -n, --no-parse-wal          do not try to parse WAL files
   -q, --quiet                 do not print any output, except for errors
   -s, --skip-checksums        skip checksum verification
   -w, --wal-directory=PATH    use specified path for WAL files
 "%s" has size %lld on disk but size %zu in the manifest "%s" is not a file or directory "%s" is present in the manifest but not on disk "%s" is present on disk but not in the manifest "\u" must be followed by four hexadecimal digits. %s home page: <%s>
 %s verifies a backup against the backup manifest.

 Character with value 0x%02x must be escaped. Escape sequence "\%s" is invalid. Expected "," or "]", but found "%s". Expected "," or "}", but found "%s". Expected ":", but found "%s". Expected JSON value, but found "%s". Expected array element or "]", but found "%s". Expected end of input, but found "%s". Expected string or "}", but found "%s". Expected string, but found "%s". Options:
 The input string ended unexpectedly. Token "%s" is invalid. Try "%s --help" for more information. Unicode escape values cannot be used for code point values above 007F when the encoding is not UTF8. Unicode high surrogate must not follow a high surrogate. Unicode low surrogate must follow a high surrogate. Usage:
  %s [OPTION]... BACKUPDIR

 WAL parsing failed for timeline %u \u0000 cannot be converted to text. backup successfully verified
 both path name and encoded path name cannot duplicate null pointer (internal error)
 checksum mismatch for file "%s" checksum without algorithm could not close directory "%s": %m could not close file "%s": %m could not decode file name could not finalize checksum of file "%s" could not finalize checksum of manifest could not initialize checksum of file "%s" could not initialize checksum of manifest could not open directory "%s": %m could not open file "%s": %m could not parse backup manifest: %s could not parse end LSN could not parse start LSN could not read file "%s": %m could not read file "%s": read %d of %lld could not stat file "%s": %m could not stat file or directory "%s": %m could not update checksum of file "%s" could not update checksum of manifest detail:  duplicate path name in backup manifest: "%s" error:  expected at least 2 lines expected version indicator file "%s" has checksum of length %d, but expected %d file "%s" should contain %zu bytes, but read %zu bytes file size is not an integer hint:  invalid checksum for file "%s": "%s" invalid manifest checksum: "%s" last line not newline-terminated manifest checksum mismatch manifest ended unexpectedly manifest has no checksum missing end LSN missing path name missing size missing start LSN missing timeline no backup directory specified out of memory out of memory
 parsing failed program "%s" is needed by %s but was not found in the same directory as "%s" program "%s" was found by "%s" but was not the same version as %s timeline is not an integer too many command-line arguments (first is "%s") unexpected WAL range field unexpected array end unexpected array start unexpected file field unexpected manifest version unexpected object end unexpected object field unexpected object start unexpected scalar unrecognized checksum algorithm: "%s" unrecognized top-level field warning:  Project-Id-Version: pg_verifybackup (PostgreSQL) 15
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2023-04-12 00:46+0000
PO-Revision-Date: 2023-04-06 11:32+0900
Last-Translator: Ioseph Kim <ioseph@uri.sarang.net>
Language-Team: PostgreSQL Korea <kr@postgresql.org>
Language: ko
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
 
문제점 보고 주소: <%s>
   -?, --help                  이 도움말을 보여주고 마침
   -V, --version               버전 정보를 보여주고 마침
   -e, --exit-on-error         오류가 있으면 작업 중지
   -i, --ignore=상대경로       지정한 경로 건너뜀
   -m, --manifest-path=경로    메니페스트 파일 경로 지정
   -n, --no-parse-wal          WAL 파일 검사 건너뜀
   -q, --quiet                 오류를 빼고 나머지는 아무 것도 안 보여줌
   -s, --skip-checksums        체크섬 검사 건너뜀
   -w, --wal-directory=경로    WAL 파일이 있는 경로 지정
 "%s" 의 디스크 크기는 %lld 이나 메니페스트 안에는 %zu 입니다. "%s" 이름은 파일이나 디렉터리가 아님 메니페스트 안에는 "%s" 개체가 있으나 디스크에는 없음 디스크에는 "%s" 개체가 있으나, 메니페스트 안에는 없음 "\u" 표기법은 뒤에 4개의 16진수가 와야합니다. %s 홈페이지: <%s>
 %s 프로그램은 백업 메니페스트로 백업을 검사합니다.

 0x%02x 값의 문자는 이스케이프 되어야함. 잘못된 이스케이프 조합: "\%s" "," 또는 "]"가 필요한데 "%s"이(가) 있음 "," 또는 "}"가 필요한데 "%s"이(가) 있음 ":"가 필요한데 "%s"이(가) 있음 JSON 값을 기대했는데, "%s" 값임 "]" 가 필요한데 "%s"이(가) 있음 입력 자료의 끝을 기대했는데, "%s" 값이 더 있음. "}"가 필요한데 "%s"이(가) 있음 문자열 값을 기대했는데, "%s" 값임 옵션들:
 입력 문자열이 예상치 않게 끝났음. 잘못된 토큰: "%s" 자세한 사항은 "%s --help" 명령으로 살펴보세요. 인코딩은 UTF8이 아닐 때 유니코드 이스케이프 값은 007F 이상 코드 포인트 값으로 사용할 수 없음. 유니코드 상위 surrogate(딸림 코드)는 상위 딸림 코드 뒤에 오면 안됨. 유니코드 상위 surrogate(딸림 코드) 뒤에는 하위 딸림 코드가 있어야 함. 사용법:
  %s [옵션]... 백업디렉터리

 타임라인 %u번의 WAL 분석 오류 \u0000 값은 text 형으로 변환할 수 없음. 백업 검사 완료
 패스 이름과 인코딩 된 패스 이름이 함께 있음 null 포인터를 중복할 수 없음 (내부 오류)
 "%s" 파일의 체크섬이 맞지 않음 알고리즘 없는 체크섬 "%s" 디렉터리를 닫을 수 없음: %m "%s" 파일을 닫을 수 없음: %m 파일 이름을 디코딩할 수 없음 "%s" 파일 체크섬을 마무리 할 수 없음 메니페스트 체크섬 마무리 작업 할 수 없음 "%s" 파일 체크섬을 초기화 할 수 없음 메니페스트 체크섬 초기화를 할 수 없음 "%s" 디렉터리 열 수 없음: %m "%s" 파일을 열 수 없음: %m 백업 메니페스트 구문 분석 실패: %s 끝 LSN 값을 분석할 수 없음 시작 LSN 값을 분석할 수 없음 "%s" 파일을 읽을 수 없음: %m "%s" 파일을 읽을 수 없음: %d 읽음, 전체 %lld "%s" 파일의 상태값을 알 수 없음: %m 파일 또는 디렉터리 "%s"의 상태를 확인할 수 없음: %m "%s" 파일 체크섬을 갱신할 수 없음 메니페스트 체크섬 갱신 할 수 없음 상세정보:  백업 메니페스트 안에 경로 이름이 중복됨: "%s" 오류:  적어도 2줄이 더 있어야 함 버전 지시자가 있어야 함 "%s" 파일 체크섬 %d, 예상되는 값: %d "%s" 파일은 %zu 바이트이나 %zu 바이트를 읽음 파일 크기가 정수가 아님 힌트:  "%s" 파일의 체크섬이 잘못됨: "%s" 잘못된 메니페스트 체크섬: "%s" 마지막 줄에 줄바꿈 문자가 없음 메니페스트 체크섬 불일치 메니페스트가 비정상적으로 끝났음 메니페스트에 체크섬 없음 끝 LSN 빠짐 패스 이름 빠짐 크기 빠짐 시작 LSN 빠짐 타임라인 빠짐 백업 디렉터리를 지정하지 않았음 메모리 부족 메모리 부족
 구문 분석 실패 "%s" 프로그램이 %s 작업에서 필요하지만 같은 "%s" 디렉터리 내에 없습니다. "%s" 프로그램을 "%s" 작업을 위해 찾았지만 %s 버전과 같지 않습니다. 타임라인이 정수가 아님 너무 많은 명령행 인자를 지정했습니다. (처음 "%s") 예상치 못한 WAL 범위 필드 비정상적인 배열 끝 비정상적인 배열 시작 예상치 못한 파일 필드 예상치 못한 메니페스트 버전 비정상적인 개체 끝 예상치 못한 개체 필드 비정상적인 개체 시작 예상치 못한 스칼라 알 수 없는 체크섬 알고리즘: "%s" 최상위 필드를 알 수 없음 경고:  