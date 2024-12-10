씨아이보드 프로젝트 - Seonggeun.shop
이 프로젝트는 **씨아이보드(CIBoard)**를 기반으로 제작된 게시판 솔루션으로, 네이버 클라우드 CentOS 서버에서 배포되었으며, seonggeun.shop 도메인으로 연결되었습니다. 또한, HTTPS 보안 인증서를 설치하여 안전한 환경에서 서비스가 운영되고 있습니다.

프로젝트 개요
씨아이보드는 CodeIgniter를 기반으로 제작된 공개형 게시판 솔루션으로, Lite 버전과 Pro 버전의 두 가지로 제공됩니다. 본 프로젝트에서는 씨아이보드의 강력한 기능과 안정성을 활용하여 사용자 친화적인 웹 서비스를 구축하였습니다.

도메인: https://seonggeun.shop
서버 환경: 네이버 클라우드 CentOS 7
보안: Let's Encrypt를 활용한 HTTPS 인증서 적용
구현 기술:
PHP 7.4 이상
CodeIgniter 3
Bootstrap 4
jQuery
주요 기능
1. 기본 기능 (씨아이보드 Lite)
환경설정: 기본 환경 설정, 레이아웃 관리
페이지 관리: 메뉴 관리, 일반 페이지 생성, 팝업 관리, FAQ
회원 관리: 회원 관리, 회원 그룹 관리, 포인트 관리, 닉네임 변경 이력
게시판 관리: 게시판 생성 및 관리, 태그 관리, 게시물 히스토리 관리
통계 기능: 접속자 통계, 회원 가입 통계, 인기 검색어 현황
2. 추가 기능 (씨아이보드 Pro)
SMS 발송: 문자 발송, 휴대폰 번호 관리
컨텐츠몰 관리: 주문 내역, 구매 내역, 상품 관리
예치금 관리: 결제 기능, 예치금 통계
기타: 레벨 업 설정, 포인트 랭킹, 설문조사, 출석체크, 본인 인증
서버 설정 및 배포
1. 네이버 클라우드 CentOS 설정
CentOS 7 Minimal 설치 후 LAMP 환경 구성
Apache, MariaDB, PHP 설치 및 설정
프로젝트 배포 디렉토리: /var/www/html/codeigniter
PHP 7.4 이상 설치 및 활성화
2. 도메인 연결
도메인: seonggeun.shop
네이버 클라우드 DNS 설정을 통해 도메인과 서버를 연결
3. HTTPS 보안 인증서 적용
Let's Encrypt 인증서를 사용하여 HTTPS 활성화:
certbot을 통해 인증서 자동 갱신 설정
SSL/TLS를 통한 데이터 암호화 제공
설치 및 실행 방법
1. 프로젝트 다운로드
bash
코드 복사
git clone https://github.com/username/repository.git
cd repository
2. 설치 요구사항
PHP 7.4 이상
MariaDB/MySQL
Apache 2.4 (mod_rewrite 활성화)
3. 데이터베이스 설정
application/config/database.php에서 데이터베이스 설정 정보를 입력합니다:

php
코드 복사
$db['default'] = array(
    'dsn'   => '',
    'hostname' => 'localhost',
    'username' => 'your_db_user',
    'password' => 'your_db_password',
    'database' => 'your_db_name',
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt' => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
);
4. 실행
Apache 서버를 재시작하여 프로젝트를 실행합니다:
bash
코드 복사
systemctl restart httpd
브라우저에서 https://seonggeun.shop를 열어 서비스를 확인합니다.
라이센스
씨아이보드 프로젝트는 CodeIgniter를 기반으로 하며, 해당 라이센스에 따라 사용 가능합니다. 상세한 내용은 씨아이보드 라이센스 페이지를 참조하세요.

리소스 및 참고 자료
CodeIgniter: https://codeigniter.com
Bootstrap: https://getbootstrap.com
jQuery: https://jquery.com
Let's Encrypt: https://letsencrypt.org
위 내용을 README.md 파일로 저장하면 프로젝트의 개요와 설치 방법을 명확히 알릴 수 있습니다. 추가로 원하는 내용이 있다면 말씀해주세요! 😊
