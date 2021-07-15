SET FOREIGN_KEY_CHECKS = 0;

# Apagando tabelas antigas por precaução
DROP TABLE IF EXISTS `prod_confeccao`;
DROP TABLE IF EXISTS `prod_confeccao_item`;
DROP TABLE IF EXISTS `prod_confeccao_item_qtde`;
DROP TABLE IF EXISTS `prod_lote_confeccao`;
DROP TABLE IF EXISTS `prod_lote_confeccao_item`;
DROP TABLE IF EXISTS `prod_lote_confeccao_item_qtde`;


DROP TABLE IF EXISTS `prod_tipo_insumo`;

CREATE TABLE `prod_tipo_insumo`
(
    `id`                 bigint(20)   NOT NULL AUTO_INCREMENT,
    `codigo`             int(11)      NOT NULL,
    `descricao`          varchar(100) NOT NULL,
    `unidade_produto_id` bigint(20)   NOT NULL,

    PRIMARY KEY (`id`),
    UNIQUE KEY `UK_prod_tipo_insumo_descricao` (`descricao`),
    UNIQUE KEY `UK_prod_tipo_insumo_codigo` (`codigo`),

    -- campo de controle
    `inserted`           datetime     NOT NULL,
    `updated`            datetime     NOT NULL,
    `version`            int(11) DEFAULT NULL,
    `estabelecimento_id` bigint(20)   NOT NULL,
    `user_inserted_id`   bigint(20)   NOT NULL,
    `user_updated_id`    bigint(20)   NOT NULL,
    KEY `K_prod_tipo_insumo_estabelecimento` (`estabelecimento_id`),
    KEY `K_prod_tipo_insumo_user_inserted` (`user_inserted_id`),
    KEY `K_prod_tipo_insumo_user_updated` (`user_updated_id`),
    CONSTRAINT `FK_prod_tipo_insumo_user_inserted` FOREIGN KEY (`user_inserted_id`) REFERENCES `sec_user` (`id`),
    CONSTRAINT `FK_prod_tipo_insumo_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `cfg_estabelecimento` (`id`),
    CONSTRAINT `FK_prod_tipo_insumo_user_updated` FOREIGN KEY (`user_updated_id`) REFERENCES `sec_user` (`id`)

) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_swedish_ci;



DROP TABLE IF EXISTS `prod_tipo_artigo`;

CREATE TABLE `prod_tipo_artigo`
(

    `id`                 bigint(20)   NOT NULL AUTO_INCREMENT,
    `codigo`             int(11)      NOT NULL,
    `descricao`          varchar(100) NOT NULL,
    `modo_calculo`       varchar(15)  NOT NULL,
    `subdepto_id`        bigint(20)   NOT NULL,

    PRIMARY KEY (`id`),
    UNIQUE KEY `UK_prod_tipo_artigo_descricao` (`descricao`),
    UNIQUE KEY `UK_prod_tipo_artigo_codigo` (`codigo`),

    -- campo de controle
    `inserted`           datetime     NOT NULL,
    `updated`            datetime     NOT NULL,
    `version`            int(11) DEFAULT NULL,
    `estabelecimento_id` bigint(20)   NOT NULL,
    `user_inserted_id`   bigint(20)   NOT NULL,
    `user_updated_id`    bigint(20)   NOT NULL,
    KEY `K_prod_tipo_artigo_estabelecimento` (`estabelecimento_id`),
    KEY `K_prod_tipo_artigo_user_inserted` (`user_inserted_id`),
    KEY `K_prod_tipo_artigo_user_updated` (`user_updated_id`),
    CONSTRAINT `FK_prod_tipo_artigo_user_inserted` FOREIGN KEY (`user_inserted_id`) REFERENCES `sec_user` (`id`),
    CONSTRAINT `FK_prod_tipo_artigo_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `cfg_estabelecimento` (`id`),
    CONSTRAINT `FK_prod_tipo_artigo_user_updated` FOREIGN KEY (`user_updated_id`) REFERENCES `sec_user` (`id`)

) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_swedish_ci;



DROP TABLE IF EXISTS `prod_insumo`;

CREATE TABLE `prod_insumo`
(
    `id`                 bigint(20)   NOT NULL AUTO_INCREMENT,
    `codigo`             int(11)      NOT NULL,
    `descricao`          varchar(200) NOT NULL,
    `marca`              varchar(200) NULL,
    `tipo_insumo_id`     bigint(20)   NOT NULL,
    `unidade_produto_id` bigint(20)   NOT NULL,

    PRIMARY KEY (`id`),
    UNIQUE KEY `UK_prod_insumo_codigo` (`codigo`),
    UNIQUE KEY `UK_prod_insumo_descricao` (`descricao`),
    KEY `K_prod_insumo_tipo_insumo` (`tipo_insumo_id`),
    CONSTRAINT `FK_prod_insumo_tipo_insumo` FOREIGN KEY (`tipo_insumo_id`) REFERENCES `prod_tipo_insumo` (`id`),

    -- campo de controle
    `inserted`           datetime     NOT NULL,
    `updated`            datetime     NOT NULL,
    `version`            int(11) DEFAULT NULL,
    `estabelecimento_id` bigint(20)   NOT NULL,
    `user_inserted_id`   bigint(20)   NOT NULL,
    `user_updated_id`    bigint(20)   NOT NULL,
    KEY `K_prod_insumo_estabelecimento` (`estabelecimento_id`),
    KEY `K_prod_insumo_user_inserted` (`user_inserted_id`),
    KEY `K_prod_insumo_user_updated` (`user_updated_id`),
    CONSTRAINT `FK_prod_insumo_user_inserted` FOREIGN KEY (`user_inserted_id`) REFERENCES `sec_user` (`id`),
    CONSTRAINT `FK_prod_insumo_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `cfg_estabelecimento` (`id`),
    CONSTRAINT `FK_prod_insumo_user_updated` FOREIGN KEY (`user_updated_id`) REFERENCES `sec_user` (`id`)

) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_swedish_ci;



DROP TABLE IF EXISTS `prod_insumo_preco`;

CREATE TABLE `prod_insumo_preco`
(
    `id`                 bigint(20)     NOT NULL AUTO_INCREMENT,
    `coeficiente`        double         NOT NULL,
    `custo_operacional`  double         NOT NULL,
    `dt_custo`           date           NOT NULL,
    `margem`             double         NOT NULL,
    `prazo`              int(11)        NOT NULL,
    `preco_custo`        double         NOT NULL,
    `preco_prazo`        double         NOT NULL,
    `preco_vista`        double         NOT NULL,
    `fornecedor_id`      bigint(20) DEFAULT NULL,
    `insumo_id`          bigint(20)     NOT NULL,
    `custo_financeiro`   decimal(19, 2) NOT NULL,
    `atual`              bit(1)         NOT NULL,

    PRIMARY KEY (`id`),
    UNIQUE KEY `UK_prod_insumo_preco` (`insumo_id`, `dt_custo`, `fornecedor_id`),

    -- campos de controle
    `inserted`           datetime       NOT NULL,
    `updated`            datetime       NOT NULL,
    `version`            int(11)    DEFAULT NULL,
    `estabelecimento_id` bigint(20)     NOT NULL,
    `user_inserted_id`   bigint(20)     NOT NULL,
    `user_updated_id`    bigint(20)     NOT NULL,
    KEY `K_prod_insumo_preco_estabelecimento` (`estabelecimento_id`),
    KEY `K_prod_insumo_preco_user_inserted` (`user_inserted_id`),
    KEY `K_prod_insumo_preco_user_updated` (`user_updated_id`),
    CONSTRAINT `FK_prod_insumo_preco_user_inserted` FOREIGN KEY (`user_inserted_id`) REFERENCES `sec_user` (`id`),
    CONSTRAINT `FK_prod_insumo_preco_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `cfg_estabelecimento` (`id`),
    CONSTRAINT `FK_prod_insumo_preco_user_updated` FOREIGN KEY (`user_updated_id`) REFERENCES `sec_user` (`id`)

) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_swedish_ci;



DROP TABLE IF EXISTS `prod_fichatecnica`;

CREATE TABLE `prod_fichatecnica`
(

    `id`                       bigint(20)     NOT NULL AUTO_INCREMENT,
    `bloqueada`                bit(1)         NOT NULL,
    `custo_operacional_padrao` double         NOT NULL,
    `descricao`                varchar(200)   NOT NULL,
    `margem_padrao`            double         NOT NULL,
    `obs`                      varchar(5000) DEFAULT NULL,
    `prazo_padrao`             int(11)        NOT NULL,
    `cliente_id`               bigint(20)     NOT NULL, -- instituição
    `pessoa_nome`              varchar(300)   NOT NULL,
    `tipo_artigo_id`           bigint(20)     NOT NULL,
    `custo_financeiro_padrao`  decimal(19, 2) NOT NULL,
    `modo_calculo`             varchar(15)    NOT NULL,
    `grade_id`                 bigint(20)     NOT NULL,
    `oculta`                   bit(1)         NOT NULL,

    PRIMARY KEY (`id`),
    UNIQUE KEY `UK_prod_fichatecnica` (`cliente_id`, `tipo_artigo_id`, `descricao`),
    KEY `K_prod_fichatecnica_tipo_artigo` (`tipo_artigo_id`),
    CONSTRAINT `FK_prod_fichatecnica_tipo_artigo` FOREIGN KEY (`tipo_artigo_id`) REFERENCES `prod_tipo_artigo` (`id`),

-- instituição
    KEY `K_prod_fichatecnica_cliente` (`cliente_id`),
    CONSTRAINT `FK_prod_fichatecnica_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `crm_cliente` (`id`),

    -- campo de controle
    `inserted`                 datetime       NOT NULL,
    `updated`                  datetime       NOT NULL,
    `version`                  int(11)       DEFAULT NULL,
    `estabelecimento_id`       bigint(20)     NOT NULL,
    `user_inserted_id`         bigint(20)     NOT NULL,
    `user_updated_id`          bigint(20)     NOT NULL,
    KEY `K_prod_fichatecnica_estabelecimento` (`estabelecimento_id`),
    KEY `K_prod_fichatecnica_user_inserted` (`user_inserted_id`),
    KEY `K_prod_fichatecnica_user_updated` (`user_updated_id`),
    CONSTRAINT `FK_prod_fichatecnica_user_inserted` FOREIGN KEY (`user_inserted_id`) REFERENCES `sec_user` (`id`),
    CONSTRAINT `FK_prod_fichatecnica_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `cfg_estabelecimento` (`id`),
    CONSTRAINT `FK_prod_fichatecnica_user_updated` FOREIGN KEY (`user_updated_id`) REFERENCES `sec_user` (`id`)

) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_swedish_ci;



DROP TABLE IF EXISTS `prod_fichatecnica_item`;

CREATE TABLE `prod_fichatecnica_item`
(
    `id`                 bigint(20) NOT NULL AUTO_INCREMENT,
    `fichatecnica_id`    bigint(20) NOT NULL,
    `insumo_id`          bigint(20) NOT NULL,

    PRIMARY KEY (`id`),
    UNIQUE KEY `UK_prod_fichatecnica_item` (`fichatecnica_id`, `insumo_id`),
    KEY `K_prod_fichatecnica_item_fichatecnica` (`fichatecnica_id`),
    KEY `K_prod_fichatecnica_item_insumo` (`insumo_id`),
    CONSTRAINT `FK_prod_fichatecnica_item_fichatecnica` FOREIGN KEY (`fichatecnica_id`) REFERENCES `prod_fichatecnica` (`id`),
    CONSTRAINT `K_prod_fichatecnica_item_insumo` FOREIGN KEY (`insumo_id`) REFERENCES `prod_insumo` (`id`),

    -- campo de controle
    `inserted`           datetime   NOT NULL,
    `updated`            datetime   NOT NULL,
    `version`            int(11) DEFAULT NULL,
    `estabelecimento_id` bigint(20) NOT NULL,
    `user_inserted_id`   bigint(20) NOT NULL,
    `user_updated_id`    bigint(20) NOT NULL,
    KEY `K_prod_fichatecnica_item_estabelecimento` (`estabelecimento_id`),
    KEY `K_prod_fichatecnica_item_user_inserted` (`user_inserted_id`),
    KEY `K_prod_fichatecnica_item_user_updated` (`user_updated_id`),
    CONSTRAINT `FK_prod_fichatecnica_item_user_inserted` FOREIGN KEY (`user_inserted_id`) REFERENCES `sec_user` (`id`),
    CONSTRAINT `FK_prod_fichatecnica_item_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `cfg_estabelecimento` (`id`),
    CONSTRAINT `FK_prod_fichatecnica_item_user_updated` FOREIGN KEY (`user_updated_id`) REFERENCES `sec_user` (`id`)

) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_swedish_ci;



DROP TABLE IF EXISTS `prod_fichatecnica_item_qtde`;

CREATE TABLE `prod_fichatecnica_item_qtde`
(
    `id`                   bigint(20) NOT NULL AUTO_INCREMENT,
    `qtde`                 decimal(15, 3) DEFAULT NULL,
    `fichatecnica_item_id` bigint(20) NOT NULL,
    `grade_tamanho_id`     bigint(20) NOT NULL,

    PRIMARY KEY (`id`),
    UNIQUE KEY `UK_prod_fichatecnica_item_qtde` (`fichatecnica_item_id`, `grade_tamanho_id`),
    KEY `K_prod_fichatecnica_item_qtde_fichatecnica_item` (`fichatecnica_item_id`),
    CONSTRAINT `FK_prod_fichatecnica_item_qtde_fichatecnica_item` FOREIGN KEY (`fichatecnica_item_id`) REFERENCES `prod_fichatecnica_item` (`id`),

    -- campo de controle
    `inserted`             datetime   NOT NULL,
    `updated`              datetime   NOT NULL,
    `version`              int(11)        DEFAULT NULL,
    `estabelecimento_id`   bigint(20) NOT NULL,
    `user_inserted_id`     bigint(20) NOT NULL,
    `user_updated_id`      bigint(20) NOT NULL,
    KEY `K_prod_fichatecnica_item_qtde_estabelecimento` (`estabelecimento_id`),
    KEY `K_prod_fichatecnica_item_qtde_user_inserted` (`user_inserted_id`),
    KEY `K_prod_fichatecnica_item_qtde_user_updated` (`user_updated_id`),
    CONSTRAINT `FK_prod_fichatecnica_item_qtde_user_inserted` FOREIGN KEY (`user_inserted_id`) REFERENCES `sec_user` (`id`),
    CONSTRAINT `FK_prod_fichatecnica_item_qtde_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `cfg_estabelecimento` (`id`),
    CONSTRAINT `FK_prod_fichatecnica_item_qtde_user_updated` FOREIGN KEY (`user_updated_id`) REFERENCES `sec_user` (`id`)

) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_swedish_ci;



DROP TABLE IF EXISTS `prod_fichatecnica_preco`;

CREATE TABLE `prod_fichatecnica_preco`
(
    `id`                 bigint(20)     NOT NULL AUTO_INCREMENT,
    `fichatecnica_id`    bigint(20)     NOT NULL,
    `coeficiente`        double         NOT NULL,
    `custo_operacional`  double         NOT NULL,
    `descricao`          varchar(200)   NOT NULL,
    `dt_custo`           date           NOT NULL,
    `margem`             double         NOT NULL,
    `prazo`              int(11)        NOT NULL,
    `preco_custo`        double         NOT NULL,
    `preco_prazo`        double         NOT NULL,
    `preco_vista`        double         NOT NULL,
    `custo_financeiro`   decimal(19, 2) NOT NULL,

    PRIMARY KEY (`id`),
    UNIQUE KEY `UK_prod_fichatecnica_preco` (`fichatecnica_id`, `descricao`),
    KEY `K_prod_fichatecnica_preco_fichatecnica` (`fichatecnica_id`),
    CONSTRAINT `FK_prod_fichatecnica_preco_fichatecnica` FOREIGN KEY (`fichatecnica_id`) REFERENCES `prod_fichatecnica` (`id`),

    -- campo de controle
    `inserted`           datetime       NOT NULL,
    `updated`            datetime       NOT NULL,
    `version`            int(11) DEFAULT NULL,
    `estabelecimento_id` bigint(20)     NOT NULL,
    `user_inserted_id`   bigint(20)     NOT NULL,
    `user_updated_id`    bigint(20)     NOT NULL,
    KEY `K_prod_fichatecnica_preco_estabelecimento` (`estabelecimento_id`),
    KEY `K_prod_fichatecnica_preco_user_inserted` (`user_inserted_id`),
    KEY `K_prod_fichatecnica_preco_user_updated` (`user_updated_id`),
    CONSTRAINT `FK_prod_fichatecnica_preco_user_inserted` FOREIGN KEY (`user_inserted_id`) REFERENCES `sec_user` (`id`),
    CONSTRAINT `FK_prod_fichatecnica_preco_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `cfg_estabelecimento` (`id`),
    CONSTRAINT `FK_prod_fichatecnica_preco_user_updated` FOREIGN KEY (`user_updated_id`) REFERENCES `sec_user` (`id`)

) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_swedish_ci;



DROP TABLE IF EXISTS `prod_lote_producao`;

CREATE TABLE `prod_lote_producao`
(
    `id`                 bigint(20)   NOT NULL AUTO_INCREMENT,
    `codigo`             int(11)      NOT NULL,
    `descricao`          varchar(200) NOT NULL,
    `dt_lote`            date    DEFAULT NULL,

    PRIMARY KEY (`id`),
    UNIQUE KEY `UK_prod_lote_producao` (`codigo`),

    -- campo de controle
    `inserted`           datetime     NOT NULL,
    `updated`            datetime     NOT NULL,
    `version`            int(11) DEFAULT NULL,
    `estabelecimento_id` bigint(20)   NOT NULL,
    `user_inserted_id`   bigint(20)   NOT NULL,
    `user_updated_id`    bigint(20)   NOT NULL,
    KEY `K_prod_lote_producao_estabelecimento` (`estabelecimento_id`),
    KEY `K_prod_lote_producao_user_inserted` (`user_inserted_id`),
    KEY `K_prod_lote_producao_user_updated` (`user_updated_id`),
    CONSTRAINT `FK_prod_lote_producao_user_inserted` FOREIGN KEY (`user_inserted_id`) REFERENCES `sec_user` (`id`),
    CONSTRAINT `FK_prod_lote_producao_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `cfg_estabelecimento` (`id`),
    CONSTRAINT `FK_prod_lote_producao_user_updated` FOREIGN KEY (`user_updated_id`) REFERENCES `sec_user` (`id`)

) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_swedish_ci;



DROP TABLE IF EXISTS `prod_lote_producao_item`;

CREATE TABLE `prod_lote_producao_item`
(
    `id`                 bigint(20) NOT NULL AUTO_INCREMENT,
    `fichatecnica_id`    bigint(20) NOT NULL,
    `lote_producao_id`   bigint(20) NOT NULL,
    `obs`                varchar(5000) DEFAULT NULL,
    `ordem`              int(11)    NOT NULL,


    KEY `K_prod_lote_producao_item_lote_producao` (`lote_producao_id`),
    CONSTRAINT `FK_prod_lote_producao_item_lote_producao` FOREIGN KEY (`lote_producao_id`) REFERENCES `prod_lote_producao` (`id`),
    KEY `K_prod_lote_producao_item_fichatecnica` (`fichatecnica_id`),
    CONSTRAINT `FK_prod_lote_producao_item_fichatecnica` FOREIGN KEY (`fichatecnica_id`) REFERENCES `prod_fichatecnica` (`id`),

    -- campo de controle
    PRIMARY KEY (`id`),
    `inserted`           datetime   NOT NULL,
    `updated`            datetime   NOT NULL,
    `version`            int(11)       DEFAULT NULL,
    `estabelecimento_id` bigint(20) NOT NULL,
    `user_inserted_id`   bigint(20) NOT NULL,
    `user_updated_id`    bigint(20) NOT NULL,
    KEY `K_prod_lote_producao_item_estabelecimento` (`estabelecimento_id`),
    KEY `K_prod_lote_producao_item_user_inserted` (`user_inserted_id`),
    KEY `K_prod_lote_producao_item_user_updated` (`user_updated_id`),
    CONSTRAINT `FK_prod_lote_producao_item_user_inserted` FOREIGN KEY (`user_inserted_id`) REFERENCES `sec_user` (`id`),
    CONSTRAINT `FK_prod_lote_producao_item_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `cfg_estabelecimento` (`id`),
    CONSTRAINT `FK_prod_lote_producao_item_user_updated` FOREIGN KEY (`user_updated_id`) REFERENCES `sec_user` (`id`)

) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_swedish_ci;



DROP TABLE IF EXISTS `prod_lote_producao_item_qtde`;

CREATE TABLE `prod_lote_producao_item_qtde`
(
    `id`                    bigint(20) NOT NULL AUTO_INCREMENT,
    `qtde`                  int(11)    NOT NULL,
    `grade_tamanho_id`      bigint(20) NOT NULL,
    `lote_producao_item_id` bigint(20) NOT NULL,

    PRIMARY KEY (`id`),
    UNIQUE KEY `UK_prod_lote_producao_item_qtde` (`lote_producao_item_id`, `grade_tamanho_id`),
    KEY `K_prod_lote_producao_item_qtde_lote_producao_item` (`lote_producao_item_id`),
    CONSTRAINT `FK_prod_lote_producao_item_qtde_lote_producao_item` FOREIGN KEY (`lote_producao_item_id`) REFERENCES `prod_lote_producao_item` (`id`),

    -- campo de controle
    `inserted`              datetime   NOT NULL,
    `updated`               datetime   NOT NULL,
    `version`               int(11) DEFAULT NULL,
    `estabelecimento_id`    bigint(20) NOT NULL,
    `user_inserted_id`      bigint(20) NOT NULL,
    `user_updated_id`       bigint(20) NOT NULL,
    KEY `K_prod_lote_producao_item_qtde_estabelecimento` (`estabelecimento_id`),
    KEY `K_prod_lote_producao_item_qtde_user_inserted` (`user_inserted_id`),
    KEY `K_prod_lote_producao_item_qtde_user_updated` (`user_updated_id`),
    CONSTRAINT `FK_prod_lote_producao_item_qtde_user_inserted` FOREIGN KEY (`user_inserted_id`) REFERENCES `sec_user` (`id`),
    CONSTRAINT `FK_prod_lote_producao_item_qtde_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `cfg_estabelecimento` (`id`),
    CONSTRAINT `FK_prod_lote_producao_item_qtde_user_updated` FOREIGN KEY (`user_updated_id`) REFERENCES `sec_user` (`id`)

) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_swedish_ci;



ALTER TABLE prod_insumo
    ADD `dt_custo` date GENERATED ALWAYS AS
        (if(
                (
                        (trim(json_unquote(json_extract(`json_data`, '$.dt_custo'))) IS NULL) OR
                        (json_extract(`json_data`, '$.dt_custo') = cast('null' as json)) OR
                        (trim(json_unquote(json_extract(`json_data`, '$.dt_custo'))) = '')
                    ),
                NULL,
                cast(json_unquote(json_extract(`json_data`, '$.dt_custo')) as date)
            )) VIRTUAL;

ALTER TABLE prod_insumo
    ADD `preco_custo` DECIMAL(15, 2) GENERATED ALWAYS AS (IF(json_data -> "$.preco_custo" = CAST('null' AS JSON) OR trim(json_data ->> "$.preco_custo") = '', NULL,
                                                             CAST(json_data ->> "$.preco_custo" AS DECIMAL)));
