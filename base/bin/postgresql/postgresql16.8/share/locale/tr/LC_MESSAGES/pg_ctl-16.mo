��    �      $  �   ,      �
      �
       &        ;     [     z     �  !   �  3   �  ?   �  H   9  D   �  C   �  E     ?   Q  ?   �  >   �  9     L   J  B   �  E   �  �      0   �  F   �  >     B   \  I   �  %   �  <     O   L  7   �     �     �     �  M   �  -   D  !   r  C   �  y   �  9   R  C   �  B   �  C     >   W  @   �  '   �  (   �  ,   (  2   U  6   �  >   �  *   �  /   )  7   Y  4   �  %   �  %   �  1     0   D  #   u     �  4   �  2   �  0     +   P  -   |  3   �     �  +   �  1   *  6   \  1   �  *   �  "   �  7     "   K  $   n  J   �     �     �  2     0   D     u  #   �  !   �     �      �  $         3  ,   T     �  4   �  %   �  $   �  "   !  !   D  u   f  F   �     #  7   7  )   o  %   �  &   �     �     �  /     &   =  0   d  .   �  -   �     �     	          ,   <      i      w      �      �      �      �      �      �      �      !     !      8!  "   Y!     |!  |  �!  )   #     B#  2   W#  6   �#  7   �#     �#     $  !   "$  ;   D$  :   �$  D   �$  J    %  M   K%  Q   �%  @   �%  5   ,&  N   b&  2   �&  [   �&  d   @'  G   �'  �   �'  4   �(  O   �(  I   !)  A   k)  K   �)  .   �)  B   (*  h   k*  E   �*     +     "+     6+  k   J+  0   �+  "   �+  T   
,  �   _,  K   �,  X   C-  S   �-  O   �-  G   @.  M   �.  (   �.  $   �.  '   $/  A   L/  J   �/  8   �/  +   0  9   >0  7   x0  6   �0  '   �0  +   1  4   ;1  -   p1  $   �1     �1  1   �1  <   2  >   P2  3   �2  5   �2  ;   �2      53  +   V3  1   �3  5   �3  A   �3  .   ,4     [4  6   z4  (   �4  &   �4  S   5      U5     v5  0   �5  7   �5     �5  0   6  "   @6     c6  #   {6  ,   �6  "   �6  ?   �6     /7  7   N7     �7      �7     �7  !   �7  i   	8  X   s8     �8  A   �8  ,   (9  &   U9  ?   |9     �9  $   �9  .   �9  :   :  ;   Y:  =   �:  =   �:  "   ;     4;  '   F;  !   n;     �;     �;     �;     �;     �;     <     #<     9<     L<     _<  /   <  1   �<  "   �<  #   =     ?       a           _          +           M   6      
         3   u   [         :      ^   ]   {   t       ;   r   }   V   )         n      c   h   #               q   X                   	   A   /            y      Y   N   ~      4   <   U   L   j   Z       9              1   w   Q       D      *       \   E           8   "       &   W   z           i         C      =   !   J       5   7   H   p   P   v              s       �   K              @   F   f   T   g   (   O   m           $   b   G   l                     .       `   %      -       R   '   d           |       B      I      o      ,   k   �   e      >   S   0                   x       2    
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
Shutdown modes are:
 
Start types are:
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
 %s is a utility to initialize, start, stop, or control a PostgreSQL server.

 %s: -S option not supported on this platform
 %s: PID file "%s" does not exist
 %s: another server might be running; trying to start server anyway
 %s: cannot be run as root
Please log in (using, e.g., "su") as the (unprivileged) user that will
own the server process.
 %s: cannot promote server; server is not in standby mode
 %s: cannot promote server; single-user server is running (PID: %d)
 %s: cannot reload server; single-user server is running (PID: %d)
 %s: cannot restart server; single-user server is running (PID: %d)
 %s: cannot set core file size limit; disallowed by hard limit
 %s: cannot stop server; single-user server is running (PID: %d)
 %s: control file appears to be corrupt
 %s: could not access directory "%s": %s
 %s: could not allocate SIDs: error code %lu
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
 %s: could not remove promote signal file "%s": %s
 %s: could not send promote signal (PID: %d): %s
 %s: could not send signal %d (PID: %d): %s
 %s: could not send stop signal (PID: %d): %s
 %s: could not start server
Examine the log output.
 %s: could not start server: %s
 %s: could not start server: error code %lu
 %s: could not start service "%s": error code %lu
 %s: could not unregister service "%s": error code %lu
 %s: could not write promote signal file "%s": %s
 %s: database system initialization failed
 %s: directory "%s" does not exist
 %s: directory "%s" is not a database cluster directory
 %s: invalid data in PID file "%s"
 %s: missing arguments for kill mode
 %s: no database directory specified and environment variable PGDATA unset
 %s: no operation specified
 %s: no server running
 %s: old server process (PID: %d) seems to be gone
 %s: option file "%s" must have exactly one line
 %s: out of memory
 %s: server did not promote in time
 %s: server did not start in time
 %s: server does not shut down
 %s: server is running (PID: %d)
 %s: service "%s" already registered
 %s: service "%s" not registered
 %s: single-user server is running (PID: %d)
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
 child process exited with exit code %d child process exited with unrecognized status %d child process was terminated by exception 0x%X child process was terminated by signal %d: %s command not executable command not found could not find a "%s" to execute could not get current working directory: %s
 out of memory out of memory
 server promoted
 server promoting
 server shutting down
 server signaled
 server started
 server starting
 server stopped
 starting server anyway
 trying to start server anyway
 waiting for server to promote... waiting for server to shut down... waiting for server to start... Project-Id-Version: pg_ctl-tr
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2023-04-24 03:48+0000
PO-Revision-Date: 2023-09-05 09:10+0200
Last-Translator: Abdullah Gülner
Language-Team: Turkish <ceviri@postgresql.org.tr>
Language: tr
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
X-Generator: Poedit 1.8.7.1
 
kill için izin verilen sinyal adları:
 
Ortak seçenekler:
 
Kaydetmek ya da kaydı silmek için seçenekler:
 
Başlamak ya da yeniden başlamak için seçenekler:
 
Durdurmak ya da yeniden başlatmak için seçenekler:
 
Kapatma modları:
 
Başlama tipleri: 
   %s unregister [-N SERVİS_ADI]
   -?, --help             bu yardımı göster, sonra çık
   -D, --pgdata=VERİDİZİNİ   verinin tutulacağı alan
   -N SERVICENAME  PostgreSQL sunucusunu kaydedeceğiniz servis adı
   -P PASSWORD     PostgreSQL sunucusunu kaydetmek için hesabın şifresi
   -S START-TYPE   PostgreSQL sunucusunu kaydedeceğiniz servis başlama tipi
   -U USERNAME     PostgreSQL sunucusunu kaydetmek için gerekli kullanıcı adı
   -V, --version          sürüm bilgisini göster, sonra çık
   -W, --no-wait          işlem bitene kadar bekleme
   -c, --core-files       postgres'in core dosyaları oluşturmasına izin ver
   -c, --core-files       bu platformda uygulanmaz
   -e SOURCE              servis olarak çalışırken loglama için olay (event) kaynağı
   -l, --log=DOSYA_ADI    sunucu loglarını DOSYA_ADI dosyasına yaz (ya da dosyanın sonuna ekle).
   -m, --mode=MOD        MOD "smart", "fast", veya "immediate" olabilir
   -o, --options=SEÇENEKLER   postgres'e (PostgreSQL sunucusu çalıştırılabilir dosyası)
                         ya da initdb'ye geçilecek komut satırı seçenekleri
   -p PATH-TO-POSTGRES    normalde gerekli değildir
   -s, --silent           sadece hataları yazar, hiç bir bilgi mesajı yazmaz
   -t, --timeout=SANİYE   -w seçeneğini kullanırken beklenecek saniye
   -w, --wait             işlem bitene kadar bekle (varsayılan)
   auto       sistem açılışında servisi otomatik başlat (varsayılan)
   demand       hizmeti talep üzerine başlat
   fast        düzgünce kapanarak direk olarak dur (varsayılan)
   immediate   tam bir kapanma gerçekleşmeden dur; yeniden başladığında kurtarma modunda açılır
   smart       tüm istemciler bağlantılarını kestikten sonra dur
  tamam
  başarısız oldu
 bekleme durduruldu
 %s bir PostgreSQL sunucusunu ilklendirmek, başlatmak, durdurmak ya da kontrol etmek için bir araçtır.

 %s: -S seçeneği bu platformda desteklenmiyor.
 %s: "%s" PID dosyası bulunamadı
 %s: başka bir sunucu çalışıyor olabilir; yine de başlatmaya çalışılıyor.
 %s: root olarak çalıştırılamaz
Lütfen  (yani "su" kullanarak) sunucu sürecine sahip olacak (yetkisiz) kullanıcı
ile sisteme giriş yapınız.
 %s: sunucu yükseltilemiyor (promote), sunucu yedek (standby) modda değil
 %s: sunucu yükseltilemedi (promote), tek kullanıcılı sunucu çalışıyor (PID: %d)
 %s: sunucu yeniden yüklenemedi, tek kullanıcılı sunucu çalışıyor (PID: %d)
 %s: sunucu başlatılamadı; tek kullanıcılı sunucu çalışıyor (PID: %d)
 %s: core boyutu ayarlanamadı; hard limit tarafından sınırlanmış.
 %s: sunucu durdurulamadı; tek kullanıcılı sunucu çalışıyor (PID: %d)
 %s: kontrol dosyası bozuk görünüyor
 %s: "%s" dizine erişim hatası: %s
 %s: SIDler ayrılamadı: Hata kodu %lu
 %s: "%s" yükseltme (promote) sinyal dosyası yaratılamadı: %s
 %s: kısıtlı andaç (restricted token) oluşturulamıyor: hata kodu %lu
 %s: "%s" komutu kullanılarak veri dizini belirlenemedi
 %s:Çalıştırılabilir dosya bulunamadı
 %s: çalıştırılabilir postgres programı bulunamadı
 %s: yetkiler için LUID'ler alınamadı: hata kodu %lu
 %s: andaç (token) bilgisi alınamadı: hata kodu %lu
 %s: "%s" PID dosyası açılamadı: %s
 %s: "%s" kayıt dosyası açılamıyor: %s
 %s: process token açma başarısız: hata kodu %lu
 %s: "%s" servisi açılamadı: hata kodu %lu
 %s: servis yöneticisi açılamadı
 %s: "%s" dosyası okunamadı
 %s: "%s" servisi kayıt edilemedi: hata kodu %lu
 %s: "%s" yükseltme (promote) sinyal dosyası slinemedi: %s
 %s: yükseltme (promote) sinyali gönderilemedi (PID: %d): %s
 %s: %d reload sinyali gönderilemedi (PID: %d): %s
 %s: durdurma sinyali başarısız oldu (PID: %d): %s
 %s: sunucu başlatılamadı
Kayıt dosyasını inceleyiniz
 %s: sunucu başlatılamadı: %s
 %s: sunucu başlatılamadı: hata kodu %lu
 %s: "%s" servisi başlatılamadı: Hata kodu %lu
 %s: "%s" servisinin kaydı silinemedi: hata kodu %lu
 %s: "%s" yükseltme (promote) sinyal dosyasına yazılamadı: %s
 %s: veritabanı ilklendirme başarısız oldu
 %s: "%s" dizini mevcut değil
 %s: "%s" dizini bir veritabanı kümesi dizini değil
 %s: "%s" PID dosyasında geçersiz veri
 %s: kill modu için eksik argümanlar
 %s: Hiçbir veritabanı dizini belirtilmemiş ve PGDATA çevresel değişkeni boş
 %s: hiçbir işlem belirtilmedi
 %s: çalışan sunucu yok
 %s: eski sunucu süreci (PID: %d) kaybolmuştur
 %s: "%s" seçenek dosyası sadece 1 satır olmalıdır
 %s: yetersiz bellek
 %s: sunucu zamanında yükseltilemedi (promote)
 %s: sunucu zamanında başlamadı
 %s: sunucu kapanmıyor
 %s: sunucu çalışıyor (PID: %d)
 %s: "%s" servisi daha önce kaydedilmiştir
 %s: "%s" servisi kayıtlı değil
 %s: sunucu, tek kullanıcı biçiminde çalışıyor (PID: %d)
 %s: "%s" PID dosyası boştur
 %s: çok fazla komut satırı argümanı (ilki : "%s")
 %s: geçersiz işlem modu "%s"
 %s: geçersiz kapanma modu "%s"
 %s: geçersiz sinyal adı "%s"
 %s: geçersiz başlama tipi "%s"
 İPUCU: "-m fast" seçeneği oturumların kendilerinin bitmesini beklemektense
oturumları aniden keser.
 Eğer -D seçeneği gözardı edilirse, PGDATA çevresel değişkeni kullanılacaktır.
 Sunucu çalışıyor mu?
 Lütfen tek kullanıcılı sunucuyu durdurun ve yeniden deneyin.
 Sunucu başladı ve bağlantı kabul ediyor
 Sunucu başlarken zaman aşımı oldu
 Daha fazla bilgi için "%s --help" komutunu kullanabilirsiniz.
 Kullanımı:
 Sunucunun başlaması bekleniyor...
 null pointer  duplicate edilemiyor (iç hata)
 alt süreç %d çıkış koduyla sonuçlandırılmıştır alt süreç %d bilinmeyen durumu ile sonlandırılmıştır alt süreç 0x%X exception tarafından sonlandırılmıştır alt süreç %d sinyali tarafından sonlandırılmıştır: %s komut çalıştırılabilir değil komut bulunamadı "%s"  çalıştırmak için bulunamadı geçerli dizin belirlenemedi: %s
 yetersiz bellek bellek yetersiz
 sunucu yükseltildi (promote)
 sunucu yükeltiliyor (promote)
 sunucu kapatılıyor
 sunucuya sinyal gönderildi
 sunucu başlatıldı
 sunucu başlıyor
 sunucu durduruldu
 sunucu yine de başlatılıyor
 sunucu yine de başlatılmaya çalışılıyor
 sunucunun yükseltilmesi (promote)  bekleniyor... sunucunun kapanması bekleniyor... sunucunun başlaması bekleniyor... 