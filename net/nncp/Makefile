# $FreeBSD$

PORTNAME=	nncp
DISTVERSION=	5.2.1
CATEGORIES=	net
MASTER_SITES=	http://www.nncpgo.org/download/

MAINTAINER=	stargrave@stargrave.org
COMMENT=	Utilities for secure store-and-forward files, mail, command exchanging

LICENSE=	GPLv3
LICENSE_FILE=	${WRKSRC}/COPYING

USES=		go:no_targets tar:xz

USE_RC_SUBR=	nncp-caller nncp-daemon nncp-toss

MAKE_ARGS=	INFODIR=${STAGEDIR}${PREFIX}/${INFO_PATH}
INSTALL_TARGET=	install-strip

SUB_FILES=	pkg-message pkg-install pkg-deinstall

INFO=		nncp
PORTDOCS=	AUTHORS NEWS NEWS.RU README README.RU THANKS

OPTIONS_DEFINE=	DOCS

post-install:
	${INSTALL_DATA} ${FILESDIR}/nncp.newsyslog.conf.sample ${STAGEDIR}${PREFIX}/etc/nncp.conf.sample

.include <bsd.port.mk>
