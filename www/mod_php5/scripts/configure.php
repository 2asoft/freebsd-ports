#!/bin/sh
# $FreeBSD$

if [ -f ${WRKDIRPREFIX}${REALCURDIR}/Makefile.inc ]; then
	exit
fi

if [ "${BATCH}" ]; then
	set \"zlib\" \"MySQL\"
else
	/usr/bin/dialog --title "configuration options" --clear \
		--checklist "\n\
Please select desired options:" -1 -1 16 \
GD		"GD library support" OFF \
FreeType	"TrueType font rendering (implies GD)" OFF \
zlib		"zlib library support" ON \
mcrypt		"Encryption support" OFF \
mhash		"Crypto-hashing support" OFF \
pdflib		"pdflib support (implies zlib)" OFF \
IMAP		"IMAP support" OFF \
MySQL		"MySQL database support" ON \
PostgreSQL	"PostgreSQL database support" OFF \
SybaseDB	"Sybase/MS-SQL database support (DB-lib)" OFF \
SybaseCT	"Sybase/MS-SQL database support (CT-lib)" OFF \
dBase		"dBase database support" OFF \
OpenLDAP	"OpenLDAP support" OFF \
SNMP		"SNMP support" OFF \
XML		"XML support" OFF \
FTP		"File Transfer Protocol support" OFF \
gettext		"gettext library support" OFF \
jstring		"jstring module" OFF \
YP		"YP/NIS support" OFF \
BCMath		"BCMath support" OFF \
ming		"ming library support" OFF \
2> /tmp/checklist.tmp.$$

	retval=$?

	if [ -s /tmp/checklist.tmp.$$ ]; then
		set `cat /tmp/checklist.tmp.$$`
	fi
	rm -f /tmp/checklist.tmp.$$

	case $retval in
		0)	if [ -z "$*" ]; then
				echo "Nothing selected"
			fi
			;;
		1)	echo "Cancel pressed."
			exit 1
			;;
	esac
fi

${MKDIR} ${WRKDIRPREFIX}${REALCURDIR}
exec > ${WRKDIRPREFIX}${REALCURDIR}/Makefile.inc

while [ "$1" ]; do
	case $1 in
		\"GD\")
			echo "LIB_DEPENDS+=	gd.2:\${PORTSDIR}/graphics/gd"
			echo "CONFIGURE_ARGS+=--with-gd=\${PREFIX}"
			GD=1
			;;
		\"FreeType\")
			echo "LIB_DEPENDS+=	ttf.4:\${PORTSDIR}/print/freetype"
			echo "CONFIGURE_ARGS+=--with-ttf=\${PREFIX}"
			if [ -z "$GD" ]; then
				set $* \"GD\"
			fi
			;;
		\"zlib\")
			echo "CONFIGURE_ARGS+=--with-zlib"
			ZLIB=1
			;;
		\"mcrypt\")
			echo "mcrypt support doesn't compile at the moment. Ignoring." > /dev/stderr
			;;
		\"nothing\")
			echo "LIB_DEPENDS+=	mcrypt.5:\${PORTSDIR}/security/libmcrypt"
			echo "CONFIGURE_ARGS+=--with-mcrypt=\${PREFIX}"
			;;
		\"mhash\")
			echo "LIB_DEPENDS+=	mhash.2:\${PORTSDIR}/security/mhash"
			echo "CONFIGURE_ARGS+=--with-mhash=\${PREFIX}"
			;;
		\"pdflib\")
			echo "pdflib is DISABLED for now. Ignoring." > /dev/stderr
			;;
		\"nothing\")
			echo "LIB_DEPENDS+=	pdf.2:\${PORTSDIR}/print/pdflib"
			echo "CONFIGURE_ARGS+=--with-pdflib=\${PREFIX} \\"
			echo "		--with-jpeg-dir=\${PREFIX} \\"
			echo "		--with-tiff-dir=\${PREFIX}"
			if [ -z "$ZLIB" ]; then
				set $* \"zlib\"
			fi
			;;
		\"IMAP\")
			echo "LIB_DEPENDS+=	c-client4.8:\${PORTSDIR}/mail/cclient"
			echo "CONFIGURE_ARGS+=--with-imap=\${PREFIX}"
			;;
		\"MySQL\")
			echo "LIB_DEPENDS+=	mysqlclient.10:\${PORTSDIR}/databases/mysql323-client"
			echo "CONFIGURE_ARGS+=--with-mysql=\${PREFIX}"
			;;
		\"PostgreSQL\")
			echo "LIB_DEPENDS+=	pq.2:\${PORTSDIR}/databases/postgresql7"
			echo "CONFIGURE_ARGS+=--with-pgsql=\${PREFIX}/pgsql"
			if /usr/bin/ldd ${PREFIX}/pgsql/bin/postgres | /usr/bin/grep -q "libssl"; then
				LIBS="-lcrypto -lssl"
			fi
			;;
		\"SybaseDB\")
			echo "LIB_DEPENDS+=	sybdb.0:\${PORTSDIR}/databases/freetds"
			echo "CONFIGURE_ARGS+=--with-sybase=\${PREFIX}"
			if [ "$SYBASECT" ]; then
				echo "SybaseDB and SybaseCT are mutually exclusive." > /dev/stderr
				rm -f ${WRKDIRPREFIX}${REALCURDIR}/Makefile.inc
				exit 1
			fi
			SYBASEDB=1
			;;
		\"SybaseCT\")
			echo "LIB_DEPENDS+=	ct.0:\${PORTSDIR}/databases/freetds"
			echo "CONFIGURE_ARGS+=--with-sybase-ct=\${PREFIX}"
			if [ "$SYBASEDB" ]; then
				echo "SybaseDB and SybaseCT are mutually exclusive." > /dev/stderr
				rm -f ${WRKDIRPREFIX}${REALCURDIR}/Makefile.inc
				exit 1
			fi
			SYBASECT=1
			;;
		\"dBase\")
			echo "CONFIGURE_ARGS+=--with-dbase"
			;;
		\"OpenLDAP\")
			echo "LIB_DEPENDS+=	ldap.1:\${PORTSDIR}/net/openldap"
			echo "LIB_DEPENDS+=	lber.1:\${PORTSDIR}/net/openldap"
			echo "CONFIGURE_ARGS+=--with-ldap=\${PREFIX}"
			if [ -f /usr/lib/libkrb.a -a -f /usr/lib/libdes.a -a ! -L /usr/lib/libdes.a ]; then
				LIBS="${LIBS} -lkrb -ldes -L\${PREFIX}/lib"
			fi
			;;
		\"SNMP\")
			echo "SNMP is DISABLED for now. Ignoring." > /dev/stderr
			;;
		\"nothing\")
			echo "LIB_DEPENDS+=	snmp.4:\${PORTSDIR}/net/net-snmp"
			echo "CONFIGURE_ARGS+=--with-snmp=\${PREFIX} --enable-ucd-snmp-hack"
			;;
		\"XML\")
			echo "BUILD_DEPENDS+=	\${PREFIX}/lib/libexpat.a:\${PORTSDIR}/textproc/expat"
			echo "BUILD_DEPENDS+=	\${PREFIX}/include/xml/xmlparse.h:\${PORTSDIR}/textproc/expat"
			echo "BUILD_DEPENDS+=	\${PREFIX}/include/xml/xmltok.h:\${PORTSDIR}/textproc/expat"
			echo "CONFIGURE_ARGS+=--with-xml=\${PREFIX}"
			;;
		\"FTP\")
			echo "CONFIGURE_ARGS+=--enable-ftp"
			;;
		\"gettext\")
			echo "LIB_DEPENDS+=	intl.1:\${PORTSDIR}/devel/gettext"
			echo "CONFIGURE_ARGS+=--with-gettext=\${PREFIX}"
			;;
		\"jstring\")
			${CAT} << EOF
MASTER_SITES+=	ftp://night.fminn.nagano.nagano.jp/php4/
DISTFILES=	\${DISTNAME}\${EXTRACT_SUFX} php-4.0RC2_jstring-1.0.tar.gz
CONFIGURE_ARGS+=--enable-jstring
BUILD_DEPENDS+=	automake:\${PORTSDIR}/devel/automake
BUILD_DEPENDS+=	autoconf:\${PORTSDIR}/devel/autoconf

post-extract-jstring:
	[ -d \${WRKDIR}/jstring ] && \\
	(cd \${WRKSRC}; \\
	 \${MV} ${WRKDIR}/jstring ext; \\
	 \${RM} configure; \\
	 ./buildconf)

EOF
			;;
		\"YP\")
			echo "CONFIGURE_ARGS+=--enable-yp"
			;;
		\"BCMath\")
			echo "CONFIGURE_ARGS+=--enable-bcmath"
			;;
		\"ming\")
			${CAT} << EOF
CONFIGURE_ARGS+=--with-ming=\${PREFIX}
BUILD_DEPENDS+=	/nonexistent:\${PORTSDIR}/graphics/ming:extract
BUILD_DEPENDS+=	automake:\${PORTSDIR}/devel/automake
BUILD_DEPENDS+=	autoconf:\${PORTSDIR}/devel/autoconf
LIB_DEPENDS+=	ming.2:\${PORTSDIR}/graphics/ming

post-extract-ming:
	[ -d \${PORTSDIR}/graphics/ming/work ] && \\
	(cd \${WRKSRC}; \\
	 \${MKDIR} \${WRKSRC}/ext/ming; \\
	 \${CP} \`cd \${PORTSDIR}/graphics/ming && \${MAKE} -V WRKSRC\`/../php_ext/* \${WRKSRC}/ext/ming; \\
	 \${CP} \${FILESDIR}/ming-config-m4 \${WRKSRC}/ext/ming/config.m4; \\
	 \${RM} configure; \\
	 ./buildconf)

EOF
			;;
		*)
			echo "Invalid option(s): $*" > /dev/stderr
			rm -f ${WRKDIRPREFIX}${REALCURDIR}/Makefile.inc
			exit 1
			;;
	esac
	shift
done

if [ "${LIBS}" ]; then
	echo "CONFIGURE_ENV+=	LIBS='${LIBS}'"
fi
