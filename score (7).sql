-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 09/09/2025 às 20:18
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `score`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `computador`
--

CREATE TABLE `computador` (
  `IP` varchar(15) NOT NULL,
  `Nome` varchar(100) NOT NULL,
  `Local` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `computador`
--

INSERT INTO `computador` (`IP`, `Nome`, `Local`) VALUES
('192.168.0.1', 'PC-Sala-1', 'Sala dos Professores'),
('192.168.0.2', 'PC-Sala-2', 'Laboratório de Informática'),
('192.168.0.3', 'PC-Sala-3', 'Secretaria'),
('192.168.0.54', 'LAB18-PC02', 'Laboratorio');

-- --------------------------------------------------------

--
-- Estrutura para tabela `mensagens`
--

CREATE TABLE `mensagens` (
  `id` int(11) NOT NULL,
  `RM_origem` int(11) NOT NULL,
  `RM_destino` int(11) NOT NULL,
  `Mensagem` text NOT NULL,
  `DataEnvio` datetime DEFAULT current_timestamp(),
  `Lida` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `mensagens`
--

INSERT INTO `mensagens` (`id`, `RM_origem`, `RM_destino`, `Mensagem`, `DataEnvio`, `Lida`) VALUES
(1, 101, 102, 'ola', '2025-09-05 14:23:19', 1),
(2, 101, 102, 'ola', '2025-09-05 14:26:14', 1),
(3, 102, 101, 'ola', '2025-09-05 14:26:42', 1),
(4, 101, 102, 'oi', '2025-09-05 14:26:56', 1),
(5, 101, 102, 'ola', '2025-09-05 14:28:18', 1),
(6, 102, 101, 'oi', '2025-09-05 14:28:31', 1),
(7, 101, 102, 'a', '2025-09-05 14:29:59', 1),
(8, 102, 101, 'a', '2025-09-05 14:30:19', 1),
(9, 101, 102, 'oi', '2025-09-05 14:33:17', 1),
(10, 101, 102, 'a', '2025-09-05 14:35:16', 1),
(12, 101, 102, 'ASD', '2025-09-05 14:52:44', 0),
(13, 101, 102, 'ASD', '2025-09-05 14:52:51', 0),
(14, 101, 102, 'ASD', '2025-09-05 14:52:58', 0),
(15, 101, 102, 'ASD', '2025-09-05 14:54:12', 0),
(16, 101, 102, 'DASDASDASD', '2025-09-05 14:54:14', 0),
(17, 101, 102, 'vaatybjnwbyigqufjwenulililililijg9pn9pjh5nu99999999999999999vaatybjnwbyigqufjwenulililililijg9pn9pjh5nu99999999999999999vaatybjnwbyigqufjwenulililililijg9pn9pjh5nu99999999999999999vaatybjnwbyigqufjwenulililililijg9pn9pjh5nu99999999999999999vaatybjnwbyigqufjwenulililililijg9pn9pjh5nu99999999999999999vaatybjnwbyigqufjwenulililililijg9pn9pjh5nu99999999999999999', '2025-09-05 14:54:28', 0),
(18, 101, 102, 'vaatybjnwbyigqufjwenulililililijg9pn9pjh5nu99999999999999999vaatybjnwb vaatybjnwbyigqufjwenulililililijg9pn9pjh5nu99999999999999999vaatybjnwb vaatybjnwbyigqufjwenulililililijg9pn9pjh5nu99999999999999999vaatybjnwb', '2025-09-05 15:10:43', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `RM` int(11) NOT NULL,
  `Nome` varchar(100) NOT NULL,
  `CPF` char(11) NOT NULL,
  `Tipo` enum('Secretaria','Professor') NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Senha` varchar(255) NOT NULL,
  `codigo_recuperacao` varchar(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`RM`, `Nome`, `CPF`, `Tipo`, `Email`, `Senha`, `codigo_recuperacao`) VALUES
(101, 'Ana Maria', '12345678901', 'Professor', 'ana.maria@example.coma', 'senha123', NULL),
(102, 'Carlos Silva', '98765432100', 'Professor', 'carlos.silva@example.com', 'senha456', NULL),
(103, 'Fernanda Costa', '32165498700', 'Secretaria', 'fernanda.costa@example.com', 'senha789', NULL),
(108, 'pedro', '49750033841', 'Professor', 'valeriojunior345@gmail.com', '123123', NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `computador`
--
ALTER TABLE `computador`
  ADD PRIMARY KEY (`IP`);

--
-- Índices de tabela `mensagens`
--
ALTER TABLE `mensagens`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`RM`),
  ADD UNIQUE KEY `CPF` (`CPF`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `mensagens`
--
ALTER TABLE `mensagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `RM` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
