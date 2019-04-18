SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `prod_tipo_insumo`;

CREATE TABLE `prod_tipo_insumo` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `codigo` int(11) NOT NULL,
  `descricao` varchar(100) COLLATE utf8_swedish_ci NOT NULL,
  `unidade_produto_id` bigint(20) NOT NULL,

  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_prod_tipo_insumo_descricao` (`descricao`),
  UNIQUE KEY `UK_prod_tipo_insumo_codigo` (`codigo`),

  -- campo de controle
  `inserted` datetime  NOT NULL,
  `updated` datetime  NOT NULL,
  `version` int(11) DEFAULT NULL,
  `estabelecimento_id` bigint(20) NOT NULL,
  `user_inserted_id` bigint(20) NOT NULL,
  `user_updated_id` bigint(20) NOT NULL,
  KEY `K_prod_tipo_insumo_estabelecimento` (`estabelecimento_id`),
  KEY `K_prod_tipo_insumo_user_inserted` (`user_inserted_id`),
  KEY `K_prod_tipo_insumo_user_updated` (`user_updated_id`),
  CONSTRAINT `FK_prod_tipo_insumo_user_inserted` FOREIGN KEY (`user_inserted_id`) REFERENCES `sec_user` (`id`),
  CONSTRAINT `FK_prod_tipo_insumo_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `cfg_estabelecimento` (`id`),
  CONSTRAINT `FK_prod_tipo_insumo_user_updated` FOREIGN KEY (`user_updated_id`) REFERENCES `sec_user` (`id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;



DROP TABLE IF EXISTS `prod_tipo_artigo`;

CREATE TABLE `prod_tipo_artigo` (

  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `codigo` int(11) NOT NULL,
  `descricao` varchar(100) COLLATE utf8_swedish_ci NOT NULL,
  `modo_calculo` varchar(15) COLLATE utf8_swedish_ci NOT NULL,
  `subdepto_id` bigint(20) NOT NULL,

  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_prod_tipo_artigo_descricao` (`descricao`),
  UNIQUE KEY `UK_prod_tipo_artigo_codigo` (`codigo`),

  -- campo de controle
  `inserted` datetime  NOT NULL,
  `updated` datetime  NOT NULL,
  `version` int(11) DEFAULT NULL,
  `estabelecimento_id` bigint(20) NOT NULL,
  `user_inserted_id` bigint(20) NOT NULL,
  `user_updated_id` bigint(20) NOT NULL,
  KEY `K_prod_tipo_artigo_estabelecimento` (`estabelecimento_id`),
  KEY `K_prod_tipo_artigo_user_inserted` (`user_inserted_id`),
  KEY `K_prod_tipo_artigo_user_updated` (`user_updated_id`),
  CONSTRAINT `FK_prod_tipo_artigo_user_inserted` FOREIGN KEY (`user_inserted_id`) REFERENCES `sec_user` (`id`),
  CONSTRAINT `FK_prod_tipo_artigo_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `cfg_estabelecimento` (`id`),
  CONSTRAINT `FK_prod_tipo_artigo_user_updated` FOREIGN KEY (`user_updated_id`) REFERENCES `sec_user` (`id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;




DROP TABLE IF EXISTS `prod_insumo`;

CREATE TABLE `prod_insumo` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `codigo` int(11) NOT NULL,
  `descricao` varchar(200) COLLATE utf8_swedish_ci NOT NULL,
  `tipo_insumo_id` bigint(20) NOT NULL,
  `unidade_produto_id` bigint(20) NOT NULL,

  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_prod_insumo_codigo` (`codigo`),
  UNIQUE KEY `UK_prod_insumo_descricao` (`descricao`),
  KEY `K_prod_insumo_tipo_insumo` (`tipo_insumo_id`),
  CONSTRAINT `FK_prod_insumo_tipo_insumo` FOREIGN KEY (`tipo_insumo_id`) REFERENCES `prod_tipo_insumo` (`id`),

  -- campo de controle
  `inserted` datetime  NOT NULL,
  `updated` datetime  NOT NULL,
  `version` int(11) DEFAULT NULL,
  `estabelecimento_id` bigint(20) NOT NULL,
  `user_inserted_id` bigint(20) NOT NULL,
  `user_updated_id` bigint(20) NOT NULL,
  KEY `K_prod_insumo_estabelecimento` (`estabelecimento_id`),
  KEY `K_prod_insumo_user_inserted` (`user_inserted_id`),
  KEY `K_prod_insumo_user_updated` (`user_updated_id`),
  CONSTRAINT `FK_prod_insumo_user_inserted` FOREIGN KEY (`user_inserted_id`) REFERENCES `sec_user` (`id`),
  CONSTRAINT `FK_prod_insumo_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `cfg_estabelecimento` (`id`),
  CONSTRAINT `FK_prod_insumo_user_updated` FOREIGN KEY (`user_updated_id`) REFERENCES `sec_user` (`id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;




DROP TABLE IF EXISTS `prod_insumo_preco`;

CREATE TABLE `prod_insumo_preco` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `coeficiente` double NOT NULL,
  `custo_operacional` double NOT NULL,
  `dt_custo` date NOT NULL,
  `margem` double NOT NULL,
  `prazo` int(11) NOT NULL,
  `preco_custo` double NOT NULL,
  `preco_prazo` double NOT NULL,
  `preco_vista` double NOT NULL,
  `fornecedor_id` bigint(20) DEFAULT NULL,
  `insumo_id` bigint(20) NOT NULL,
  `custo_financeiro` decimal(19,2) NOT NULL,
  `atual` bit(1) NOT NULL,

  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_prod_insumo_preco` (`insumo_id`,`dt_custo`,`fornecedor_id`),

  -- campos de controle
  `inserted` datetime  NOT NULL,
  `updated` datetime  NOT NULL,
  `version` int(11) DEFAULT NULL,
  `estabelecimento_id` bigint(20) NOT NULL,
  `user_inserted_id` bigint(20) NOT NULL,
  `user_updated_id` bigint(20) NOT NULL,
  KEY `K_prod_insumo_preco_estabelecimento` (`estabelecimento_id`),
  KEY `K_prod_insumo_preco_user_inserted` (`user_inserted_id`),
  KEY `K_prod_insumo_preco_user_updated` (`user_updated_id`),
  CONSTRAINT `FK_prod_insumo_preco_user_inserted` FOREIGN KEY (`user_inserted_id`) REFERENCES `sec_user` (`id`),
  CONSTRAINT `FK_prod_insumo_preco_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `cfg_estabelecimento` (`id`),
  CONSTRAINT `FK_prod_insumo_preco_user_updated` FOREIGN KEY (`user_updated_id`) REFERENCES `sec_user` (`id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;


DROP TABLE IF EXISTS `prod_instituicao`;

CREATE TABLE `prod_instituicao` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `codigo` int(11) NOT NULL,
  `nome` varchar(100) COLLATE utf8_swedish_ci NOT NULL,
  `obs` varchar(5000) COLLATE utf8_swedish_ci DEFAULT NULL,
  `pessoa_id` bigint(20) DEFAULT NULL,
  `cliente_id` bigint(20) DEFAULT NULL,
  `fornecedor_id` bigint(20) DEFAULT NULL,

  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_prod_instituicao_nome` (`nome`),
  UNIQUE KEY `UK_prod_instituicao_codigo` (`codigo`),
  UNIQUE KEY `UK_prod_instituicao_pessoa_id` (`pessoa_id`),

    -- campo de controle
  `inserted` datetime  NOT NULL,
  `updated` datetime  NOT NULL,
  `version` int(11) DEFAULT NULL,
  `estabelecimento_id` bigint(20) NOT NULL,
  `user_inserted_id` bigint(20) NOT NULL,
  `user_updated_id` bigint(20) NOT NULL,
  KEY `K_prod_instituicao_estabelecimento` (`estabelecimento_id`),
  KEY `K_prod_instituicao_user_inserted` (`user_inserted_id`),
  KEY `K_prod_instituicao_user_updated` (`user_updated_id`),
  CONSTRAINT `FK_prod_instituicao_user_inserted` FOREIGN KEY (`user_inserted_id`) REFERENCES `sec_user` (`id`),
  CONSTRAINT `FK_prod_instituicao_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `cfg_estabelecimento` (`id`),
  CONSTRAINT `FK_prod_instituicao_user_updated` FOREIGN KEY (`user_updated_id`) REFERENCES `sec_user` (`id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;




DROP TABLE IF EXISTS `prod_confeccao`;

CREATE TABLE `prod_confeccao` (

  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `bloqueada` bit(1) NOT NULL,
  `custo_operacional_padrao` double NOT NULL,
  `descricao` varchar(200) COLLATE utf8_swedish_ci NOT NULL,
  `margem_padrao` double NOT NULL,
  `obs` varchar(5000) COLLATE utf8_swedish_ci DEFAULT NULL,
  `prazo_padrao` int(11) NOT NULL,
  `instituicao_id` bigint(20) NOT NULL,
  `tipo_artigo_id` bigint(20) NOT NULL,
  `custo_financeiro_padrao` decimal(19,2) NOT NULL,
  `modo_calculo` varchar(15) COLLATE utf8_swedish_ci NOT NULL,
  `grade_id` bigint(20) NOT NULL,
  `oculta` bit(1) NOT NULL,

  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_prod_confeccao` (`instituicao_id`,`tipo_artigo_id`,`descricao`),
  KEY `K_prod_confeccao_tipo_artigo` (`tipo_artigo_id`),
  KEY `K_prod_confeccao_instituicao` (`instituicao_id`),
  CONSTRAINT `FK_prod_confeccao_tipo_artigo` FOREIGN KEY (`tipo_artigo_id`) REFERENCES `prod_tipo_artigo` (`id`),
  CONSTRAINT `FK_prod_confeccao_instituicao` FOREIGN KEY (`instituicao_id`) REFERENCES `prod_instituicao` (`id`),

    -- campo de controle
  `inserted` datetime  NOT NULL,
  `updated` datetime  NOT NULL,
  `version` int(11) DEFAULT NULL,
  `estabelecimento_id` bigint(20) NOT NULL,
  `user_inserted_id` bigint(20) NOT NULL,
  `user_updated_id` bigint(20) NOT NULL,
  KEY `K_prod_confeccao_estabelecimento` (`estabelecimento_id`),
  KEY `K_prod_confeccao_user_inserted` (`user_inserted_id`),
  KEY `K_prod_confeccao_user_updated` (`user_updated_id`),
  CONSTRAINT `FK_prod_confeccao_user_inserted` FOREIGN KEY (`user_inserted_id`) REFERENCES `sec_user` (`id`),
  CONSTRAINT `FK_prod_confeccao_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `cfg_estabelecimento` (`id`),
  CONSTRAINT `FK_prod_confeccao_user_updated` FOREIGN KEY (`user_updated_id`) REFERENCES `sec_user` (`id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;




DROP TABLE IF EXISTS `prod_confeccao_item`;

CREATE TABLE `prod_confeccao_item` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `confeccao_id` bigint(20) NOT NULL,
  `insumo_id` bigint(20) NOT NULL,

  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_prod_confeccao_item` (`confeccao_id`,`insumo_id`),
  KEY `K_prod_confeccao_item_confeccao` (`confeccao_id`),
  KEY `K_prod_confeccao_item_insumo` (`insumo_id`),
  CONSTRAINT `FK_prod_confeccao_item_confeccao` FOREIGN KEY (`confeccao_id`) REFERENCES `prod_confeccao` (`id`),
  CONSTRAINT `K_prod_confeccao_item_insumo` FOREIGN KEY (`insumo_id`) REFERENCES `prod_insumo` (`id`),

    -- campo de controle
  `inserted` datetime  NOT NULL,
  `updated` datetime  NOT NULL,
  `version` int(11) DEFAULT NULL,
  `estabelecimento_id` bigint(20) NOT NULL,
  `user_inserted_id` bigint(20) NOT NULL,
  `user_updated_id` bigint(20) NOT NULL,
  KEY `K_prod_confeccao_item_estabelecimento` (`estabelecimento_id`),
  KEY `K_prod_confeccao_item_user_inserted` (`user_inserted_id`),
  KEY `K_prod_confeccao_item_user_updated` (`user_updated_id`),
  CONSTRAINT `FK_prod_confeccao_item_user_inserted` FOREIGN KEY (`user_inserted_id`) REFERENCES `sec_user` (`id`),
  CONSTRAINT `FK_prod_confeccao_item_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `cfg_estabelecimento` (`id`),
  CONSTRAINT `FK_prod_confeccao_item_user_updated` FOREIGN KEY (`user_updated_id`) REFERENCES `sec_user` (`id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;




DROP TABLE IF EXISTS `prod_confeccao_item_qtde`;

CREATE TABLE `prod_confeccao_item_qtde` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `qtde` decimal(15,3) DEFAULT NULL,
  `confeccao_item_id` bigint(20) NOT NULL,
  `grade_tamanho_id` bigint(20) NOT NULL,

  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_prod_confeccao_item_qtde` (`confeccao_item_id`,`grade_tamanho_id`),
  KEY `K_prod_confeccao_item_qtde_confeccao_item` (`confeccao_item_id`),
  CONSTRAINT `FK_prod_confeccao_item_qtde_confeccao_item` FOREIGN KEY (`confeccao_item_id`) REFERENCES `prod_confeccao_item` (`id`),

    -- campo de controle
  `inserted` datetime  NOT NULL,
  `updated` datetime  NOT NULL,
  `version` int(11) DEFAULT NULL,
  `estabelecimento_id` bigint(20) NOT NULL,
  `user_inserted_id` bigint(20) NOT NULL,
  `user_updated_id` bigint(20) NOT NULL,
  KEY `K_prod_confeccao_item_qtde_estabelecimento` (`estabelecimento_id`),
  KEY `K_prod_confeccao_item_qtde_user_inserted` (`user_inserted_id`),
  KEY `K_prod_confeccao_item_qtde_user_updated` (`user_updated_id`),
  CONSTRAINT `FK_prod_confeccao_item_qtde_user_inserted` FOREIGN KEY (`user_inserted_id`) REFERENCES `sec_user` (`id`),
  CONSTRAINT `FK_prod_confeccao_item_qtde_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `cfg_estabelecimento` (`id`),
  CONSTRAINT `FK_prod_confeccao_item_qtde_user_updated` FOREIGN KEY (`user_updated_id`) REFERENCES `sec_user` (`id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;



DROP TABLE IF EXISTS `prod_confeccao_preco`;

CREATE TABLE `prod_confeccao_preco` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `confeccao_id` bigint(20) NOT NULL,
  `coeficiente` double NOT NULL,
  `custo_operacional` double NOT NULL,
  `descricao` varchar(200) COLLATE utf8_swedish_ci NOT NULL,
  `dt_custo` date NOT NULL,
  `margem` double NOT NULL,
  `prazo` int(11) NOT NULL,
  `preco_custo` double NOT NULL,
  `preco_prazo` double NOT NULL,
  `preco_vista` double NOT NULL,
  `custo_financeiro` decimal(19,2) NOT NULL,

  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_prod_confeccao_preco` (`confeccao_id`,`descricao`),
  KEY `K_prod_confeccao_preco_confeccao` (`confeccao_id`),
  CONSTRAINT `FK_prod_confeccao_preco_confeccao` FOREIGN KEY (`confeccao_id`) REFERENCES `prod_confeccao` (`id`),

    -- campo de controle
  `inserted` datetime  NOT NULL,
  `updated` datetime  NOT NULL,
  `version` int(11) DEFAULT NULL,
  `estabelecimento_id` bigint(20) NOT NULL,
  `user_inserted_id` bigint(20) NOT NULL,
  `user_updated_id` bigint(20) NOT NULL,
  KEY `K_prod_confeccao_preco_estabelecimento` (`estabelecimento_id`),
  KEY `K_prod_confeccao_preco_user_inserted` (`user_inserted_id`),
  KEY `K_prod_confeccao_preco_user_updated` (`user_updated_id`),
  CONSTRAINT `FK_prod_confeccao_preco_user_inserted` FOREIGN KEY (`user_inserted_id`) REFERENCES `sec_user` (`id`),
  CONSTRAINT `FK_prod_confeccao_preco_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `cfg_estabelecimento` (`id`),
  CONSTRAINT `FK_prod_confeccao_preco_user_updated` FOREIGN KEY (`user_updated_id`) REFERENCES `sec_user` (`id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;




DROP TABLE IF EXISTS `prod_lote_confeccao`;

CREATE TABLE `prod_lote_confeccao` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `codigo` int(11) NOT NULL,
  `descricao` varchar(200) COLLATE utf8_swedish_ci NOT NULL,
  `dt_lote` date DEFAULT NULL,

  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_prod_lote_confeccao` (`codigo`),

    -- campo de controle
  `inserted` datetime  NOT NULL,
  `updated` datetime  NOT NULL,
  `version` int(11) DEFAULT NULL,
  `estabelecimento_id` bigint(20) NOT NULL,
  `user_inserted_id` bigint(20) NOT NULL,
  `user_updated_id` bigint(20) NOT NULL,
  KEY `K_prod_lote_confeccao_estabelecimento` (`estabelecimento_id`),
  KEY `K_prod_lote_confeccao_user_inserted` (`user_inserted_id`),
  KEY `K_prod_lote_confeccao_user_updated` (`user_updated_id`),
  CONSTRAINT `FK_prod_lote_confeccao_user_inserted` FOREIGN KEY (`user_inserted_id`) REFERENCES `sec_user` (`id`),
  CONSTRAINT `FK_prod_lote_confeccao_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `cfg_estabelecimento` (`id`),
  CONSTRAINT `FK_prod_lote_confeccao_user_updated` FOREIGN KEY (`user_updated_id`) REFERENCES `sec_user` (`id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;




DROP TABLE IF EXISTS `prod_lote_confeccao_item`;

CREATE TABLE `prod_lote_confeccao_item` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `confeccao_id` bigint(20) NOT NULL,
  `lote_confeccao_id` bigint(20) NOT NULL,
  `obs` varchar(5000) COLLATE utf8_swedish_ci NOT NULL,
  `ordem` int(11) NOT NULL,
  
  PRIMARY KEY (`id`),
  KEY `K_prod_lote_confeccao_item_lote_confeccao` (`lote_confeccao_id`),
  CONSTRAINT `FK_prod_lote_confeccao_item_lote_confeccao` FOREIGN KEY (`lote_confeccao_id`) REFERENCES `prod_lote_confeccao` (`id`),
  KEY `K_prod_lote_confeccao_item_confeccao` (`confeccao_id`),
  CONSTRAINT `FK_prod_lote_confeccao_item_confeccao` FOREIGN KEY (`confeccao_id`) REFERENCES `prod_confeccao` (`id`),
  
  -- campo de controle
  `inserted` datetime  NOT NULL,
  `updated` datetime  NOT NULL,
  `version` int(11) DEFAULT NULL,
  `estabelecimento_id` bigint(20) NOT NULL,
  `user_inserted_id` bigint(20) NOT NULL,
  `user_updated_id` bigint(20) NOT NULL,
  KEY `K_prod_lote_confeccao_item_estabelecimento` (`estabelecimento_id`),
  KEY `K_prod_lote_confeccao_item_user_inserted` (`user_inserted_id`),
  KEY `K_prod_lote_confeccao_item_user_updated` (`user_updated_id`),
  CONSTRAINT `FK_prod_lote_confeccao_item_user_inserted` FOREIGN KEY (`user_inserted_id`) REFERENCES `sec_user` (`id`),
  CONSTRAINT `FK_prod_lote_confeccao_item_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `cfg_estabelecimento` (`id`),
  CONSTRAINT `FK_prod_lote_confeccao_item_user_updated` FOREIGN KEY (`user_updated_id`) REFERENCES `sec_user` (`id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;




DROP TABLE IF EXISTS `prod_lote_confeccao_item_qtde`;

CREATE TABLE `prod_lote_confeccao_item_qtde` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `qtde` int(11) NOT NULL,
  `grade_tamanho_id` bigint(20) NOT NULL,
  `lote_confeccao_item_id` bigint(20) NOT NULL,

  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_prod_lote_confeccao_item_qtde` (`lote_confeccao_item_id`,`grade_tamanho_id`),
  KEY `K_prod_lote_confeccao_item_qtde_lote_confeccao_item` (`lote_confeccao_item_id`),
  CONSTRAINT `FK_prod_lote_confeccao_item_qtde_lote_confeccao_item` FOREIGN KEY (`lote_confeccao_item_id`) REFERENCES `prod_lote_confeccao_item` (`id`),

  -- campo de controle
  `inserted` datetime  NOT NULL,
  `updated` datetime  NOT NULL,
  `version` int(11) DEFAULT NULL,
  `estabelecimento_id` bigint(20) NOT NULL,
  `user_inserted_id` bigint(20) NOT NULL,
  `user_updated_id` bigint(20) NOT NULL,
  KEY `K_prod_lote_confeccao_item_qtde_estabelecimento` (`estabelecimento_id`),
  KEY `K_prod_lote_confeccao_item_qtde_user_inserted` (`user_inserted_id`),
  KEY `K_prod_lote_confeccao_item_qtde_user_updated` (`user_updated_id`),
  CONSTRAINT `FK_prod_lote_confeccao_item_qtde_user_inserted` FOREIGN KEY (`user_inserted_id`) REFERENCES `sec_user` (`id`),
  CONSTRAINT `FK_prod_lote_confeccao_item_qtde_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `cfg_estabelecimento` (`id`),
  CONSTRAINT `FK_prod_lote_confeccao_item_qtde_user_updated` FOREIGN KEY (`user_updated_id`) REFERENCES `sec_user` (`id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;