-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 21/03/2025 às 04:37
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12
SET
    SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET
    time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;

/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;

/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;

/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `contacts_api`
--
-- --------------------------------------------------------
--
-- Estrutura para tabela `contatos`
--
CREATE TABLE
    `contatos` (
        `id` int (10) UNSIGNED NOT NULL,
        `usuario_id` int (10) UNSIGNED NOT NULL,
        `nome` varchar(100) NOT NULL,
        `observacao` text DEFAULT NULL,
        `photo` varchar(255) DEFAULT NULL,
        `criado_em` datetime DEFAULT current_timestamp(),
        `atualizado_em` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Despejando dados para a tabela `contatos`
--
INSERT INTO
    `contatos` (
        `id`,
        `usuario_id`,
        `nome`,
        `observacao`,
        `photo`,
        `criado_em`,
        `atualizado_em`
    )
VALUES
    (
        7,
        5,
        'Carlos',
        NULL,
        NULL,
        '2025-03-20 23:42:02',
        '2025-03-21 00:32:17'
    ),
    (
        8,
        5,
        'Carlos',
        NULL,
        NULL,
        '2025-03-20 23:42:05',
        '2025-03-20 23:42:05'
    ),
    (
        9,
        5,
        'Carlos EMail',
        NULL,
        NULL,
        '2025-03-20 23:42:08',
        '2025-03-21 00:31:24'
    );

-- --------------------------------------------------------
--
-- Estrutura para tabela `emails`
--
CREATE TABLE
    `emails` (
        `id` int (10) UNSIGNED NOT NULL,
        `contato_id` int (10) UNSIGNED NOT NULL,
        `email` varchar(100) NOT NULL,
        `tipo` enum ('pessoal', 'profissional', 'outro') DEFAULT 'pessoal',
        `observacao` text DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Despejando dados para a tabela `emails`
--
INSERT INTO
    `emails` (`id`, `contato_id`, `email`, `tipo`, `observacao`)
VALUES
    (1, 7, 'carlos@email.com', 'pessoal', NULL),
    (2, 8, 'carlos@email.com', 'pessoal', NULL),
    (3, 9, 'carlos@email.com', 'pessoal', NULL),
    (
        4,
        7,
        'joao@email.com',
        'profissional',
        'e-mail da empresa'
    ),
    (
        5,
        7,
        'maria@email.com',
        'profissional',
        'e-mail da empresa'
    );

-- --------------------------------------------------------
--
-- Estrutura para tabela `enderecos`
--
CREATE TABLE
    `enderecos` (
        `id` int (10) UNSIGNED NOT NULL,
        `contato_id` int (10) UNSIGNED NOT NULL,
        `tipo` enum ('residencial', 'comercial', 'cobranca', 'outro') DEFAULT 'outro',
        `rua` varchar(100) NOT NULL,
        `numero` varchar(20) DEFAULT NULL,
        `complemento` varchar(100) DEFAULT NULL,
        `bairro` varchar(50) DEFAULT NULL,
        `cidade` varchar(50) DEFAULT NULL,
        `estado` varchar(2) DEFAULT NULL,
        `cep` varchar(10) DEFAULT NULL,
        `observacao` text DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Despejando dados para a tabela `enderecos`
--
INSERT INTO
    `enderecos` (
        `id`,
        `contato_id`,
        `tipo`,
        `rua`,
        `numero`,
        `complemento`,
        `bairro`,
        `cidade`,
        `estado`,
        `cep`,
        `observacao`
    )
VALUES
    (
        1,
        7,
        'outro',
        'Av. Brasil',
        '123',
        '',
        '',
        'São Paulo',
        'SP',
        '',
        NULL
    ),
    (
        2,
        8,
        'outro',
        'Av. Brasil',
        '123',
        '',
        '',
        'São Paulo',
        'SP',
        '',
        NULL
    ),
    (
        3,
        9,
        'outro',
        'Av. Brasil',
        '123',
        '',
        '',
        'São Paulo',
        'SP',
        '',
        NULL
    ),
    (
        4,
        7,
        'residencial',
        'Av. Brasil',
        '123',
        '',
        'Centro',
        'São Paulo',
        'SP',
        '01000-000',
        NULL
    );

-- --------------------------------------------------------
--
-- Estrutura para tabela `telefones`
--
CREATE TABLE
    `telefones` (
        `id` int (10) UNSIGNED NOT NULL,
        `contato_id` int (10) UNSIGNED NOT NULL,
        `tipo` enum ('residencial', 'comercial', 'celular', 'whatsapp') DEFAULT 'celular',
        `numero` varchar(20) NOT NULL,
        `observacao` text DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Despejando dados para a tabela `telefones`
--
INSERT INTO
    `telefones` (
        `id`,
        `contato_id`,
        `tipo`,
        `numero`,
        `observacao`
    )
VALUES
    (1, 7, 'celular', '11999999999', NULL),
    (2, 8, 'celular', '11999999999', NULL),
    (3, 9, 'celular', '11999999999', NULL),
    (4, 7, 'whatsapp', '11999999999', 'Número pessoal');

-- --------------------------------------------------------
--
-- Estrutura para tabela `usuarios_api`
--
CREATE TABLE
    `usuarios_api` (
        `id` int (10) UNSIGNED NOT NULL,
        `nome` varchar(100) NOT NULL,
        `email` varchar(100) NOT NULL,
        `senha` varchar(255) NOT NULL,
        `session_id` varchar(128) DEFAULT NULL,
        `ultimo_login` datetime DEFAULT NULL,
        `ultima_atividade` datetime DEFAULT NULL,
        `ip_ultimo_acesso` varchar(45) DEFAULT NULL,
        `user_agent` text DEFAULT NULL,
        `ativo` tinyint (1) DEFAULT 1,
        `bloqueado` tinyint (1) DEFAULT 0,
        `criado_em` datetime DEFAULT current_timestamp(),
        `atualizado_em` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios_api`
--
INSERT INTO
    `usuarios_api` (
        `id`,
        `nome`,
        `email`,
        `senha`,
        `session_id`,
        `ultimo_login`,
        `ultima_atividade`,
        `ip_ultimo_acesso`,
        `user_agent`,
        `ativo`,
        `bloqueado`,
        `criado_em`,
        `atualizado_em`
    )
VALUES
    (
        5,
        'Rafael Souza3',
        'rafael@email.com',
        '$2y$10$mdFiWduJ2ot9afjP.qjcyuz/KFVB81xYQhpLZyofoYJ9D6UxNhrz.',
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        1,
        0,
        '2025-03-20 23:19:01',
        '2025-03-20 23:20:44'
    ),
    (
        6,
        'Rafael Souza4',
        'rafael4@email.com',
        '$2y$10$SFpDFqjLhnkhenJdbB3zqOc.6zzEBIxKfMd2X8ErzQCgm7JfyQGRy',
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        1,
        0,
        '2025-03-20 23:19:36',
        '2025-03-20 23:19:36'
    ),
    (
        7,
        'Rafael Souza5',
        'rafael5@email.com',
        '$2y$10$KTIdAIB6HPS500Q372nqve1mx1h1RKE.QQ39hyKv0H9MV3sdIpegC',
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        1,
        0,
        '2025-03-20 23:19:49',
        '2025-03-20 23:19:49'
    ),
    (
        8,
        'Rafael Souza6',
        'rafael6@email.com',
        '$2y$10$7Dqnzvh3s8ze3Gb4ydzSO.7jRSMRyeNn5PTrAQhYYFDHg7RDXlYb6',
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        1,
        0,
        '2025-03-20 23:19:58',
        '2025-03-20 23:19:58'
    ),
    (
        10,
        'Rafael Souza7',
        'rafael7@email.com',
        '$2y$10$t4Wvqn4s6h2gKxXQq3vWeOZsH5WziAtSHSD9K5BynC4mYc18yf8.6',
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        1,
        0,
        '2025-03-20 23:21:06',
        '2025-03-20 23:21:06'
    ),
    (
        11,
        'Rafael Souza8',
        'rafael8@email.com',
        '$2y$10$q8nelmClSz9WstEhPLBOk.6DFGp09Wp2i9b//wBiZhLRsO5ra8eqK',
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        1,
        0,
        '2025-03-20 23:21:14',
        '2025-03-20 23:21:14'
    );

--
-- Índices para tabelas despejadas
--
--
-- Índices de tabela `contatos`
--
ALTER TABLE `contatos` ADD PRIMARY KEY (`id`),
ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `emails`
--
ALTER TABLE `emails` ADD PRIMARY KEY (`id`),
ADD KEY `contato_id` (`contato_id`);

--
-- Índices de tabela `enderecos`
--
ALTER TABLE `enderecos` ADD PRIMARY KEY (`id`),
ADD KEY `contato_id` (`contato_id`);

--
-- Índices de tabela `telefones`
--
ALTER TABLE `telefones` ADD PRIMARY KEY (`id`),
ADD KEY `contato_id` (`contato_id`);

--
-- Índices de tabela `usuarios_api`
--
ALTER TABLE `usuarios_api` ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--
--
-- AUTO_INCREMENT de tabela `contatos`
--
ALTER TABLE `contatos` MODIFY `id` int (10) UNSIGNED NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 10;

--
-- AUTO_INCREMENT de tabela `emails`
--
ALTER TABLE `emails` MODIFY `id` int (10) UNSIGNED NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 6;

--
-- AUTO_INCREMENT de tabela `enderecos`
--
ALTER TABLE `enderecos` MODIFY `id` int (10) UNSIGNED NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 5;

--
-- AUTO_INCREMENT de tabela `telefones`
--
ALTER TABLE `telefones` MODIFY `id` int (10) UNSIGNED NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 5;

--
-- AUTO_INCREMENT de tabela `usuarios_api`
--
ALTER TABLE `usuarios_api` MODIFY `id` int (10) UNSIGNED NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 12;

--
-- Restrições para tabelas despejadas
--
--
-- Restrições para tabelas `contatos`
--
ALTER TABLE `contatos` ADD CONSTRAINT `contatos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios_api` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `emails`
--
ALTER TABLE `emails` ADD CONSTRAINT `emails_ibfk_1` FOREIGN KEY (`contato_id`) REFERENCES `contatos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `enderecos`
--
ALTER TABLE `enderecos` ADD CONSTRAINT `enderecos_ibfk_1` FOREIGN KEY (`contato_id`) REFERENCES `contatos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `telefones`
--
ALTER TABLE `telefones` ADD CONSTRAINT `telefones_ibfk_1` FOREIGN KEY (`contato_id`) REFERENCES `contatos` (`id`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;

/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;