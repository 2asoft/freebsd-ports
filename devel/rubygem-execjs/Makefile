# Created by: Steve Wills <swills@FreeBSD.org>
# $FreeBSD$

PORTNAME=	execjs
PORTVERSION=	2.7.0
CATEGORIES=	devel rubygems
MASTER_SITES=	RG

MAINTAINER=	ruby@FreeBSD.org
COMMENT=	ExecJS lets you run JavaScript code from Ruby

LICENSE=	MIT
LICENSE_FILE=	${WRKSRC}/MIT-LICENSE

USES=		gem
USE_RUBY=	yes

NO_ARCH=	yes

OPTIONS_SINGLE=	BACKEND
OPTIONS_SINGLE_BACKEND=	NODE NODE8 NODE10
OPTIONS_DEFAULT=NODE
NODE_DESC=	Use Node.js 11.x/12.x (www/node) as backend
NODE8_DESC=	Use Node.js 8.x (www/node8) as backend
NODE10_DESC=	Use Node.js 10.x (www/node10) as backend

NODE_RUN_DEPENDS=	node>=0:www/node
NODE8_RUN_DEPENDS=	node8>=0:www/node8
NODE10_RUN_DEPENDS=	node10>=0:www/node10

post-patch:
	@${REINPLACE_CMD} -e 's#%%LOCALBASE%%#${LOCALBASE}#' ${WRKSRC}/lib/execjs/runtimes.rb

.include <bsd.port.mk>
