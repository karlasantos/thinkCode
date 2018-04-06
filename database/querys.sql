-- noinspection SqlNoDataSourceInspectionForFile

-- Insere as Linguagens
INSERT INTO languages(id, name) VALUES (1, 'Linguagem C'), (2, 'Portugol');

-- Insere os conectivos lógicos da Linguagem C e cria os relacionamentos
INSERT INTO logical_connectives(id, name) VALUES (1, '&&'), (2, '||'), (3, '!');
INSERT INTO language__logical_connective(logical_connective_id, language_id) VALUES (1,1), (2,1), (3,1);

-- Insere os conectivos lógicos do Portugol e cria os relacionamentos
INSERT INTO logical_connectives(id, name) VALUES (4, 'e'), (5, 'ou'), (6, 'nao'), (7, 'xou');
INSERT INTO language__logical_connective(logical_connective_id, language_id) VALUES (4,2), (5,2), (6,2), (7,2);

\SET TYPE_CONDICIONAL = 'condicional';
\SET TYPE_REPETICAO = 'repeticao';

-- Insere os comandos de desvio da Linguagem C e cria os relacionamentos
INSERT INTO diversion_commands(id, initial_command_name, terminal_command_name, type) VALUES
(1, 'if'     , '}', :TYPE_CONDICIONAL),
(2, 'else'   , '}', :TYPE_CONDICIONAL),
(3, 'elseif' , '}', :TYPE_CONDICIONAL),
(4, 'switch' , '}', :TYPE_CONDICIONAL), --todo rever este
(5, 'case'   , '}', :TYPE_CONDICIONAL), --todo rever este
(6, 'default', '}', :TYPE_CONDICIONAL), --todo rever este
(7, 'for'    , '}', :TYPE_REPETICAO  ),
(8, 'while'  , '}', :TYPE_REPETICAO  ),
(9, 'do'     , '}', :TYPE_REPETICAO  ), --todo rever este
INSERT INTO language__bypass_command(bypass_command_id, language_id) VALUES
(1,1),
(2,1),
(3,1);
(4,1);
(5,1);
(6,1);
(7,1);
(8,1);
(9,1);

-- Insere os comandos do Portugol e cria os relacionamentos
INSERT INTO diversion_commands(id, initial_command_name, terminal_command_name, type) VALUES
(10, 'se'       , '}'           , :TYPE_CONDICIONAL), --todo rever o final deste comando
(11, 'senao'    , 'fim-se'      , :TYPE_CONDICIONAL),
(12, 'elseif'   , '}'           , :TYPE_CONDICIONAL), --todo verificar se este não existe
(13, 'escolha'  , 'fim-ecolha'  , :TYPE_CONDICIONAL),
(14, 'caso'     , '}'           , :TYPE_CONDICIONAL), --todo rever o final deste comando
(15, 'outrocaso', '}'           , :TYPE_CONDICIONAL), --todo rever o final deste comando
(16, 'para'     , 'fim-para'    , :TYPE_REPETICAO  ),
(17, 'enquanto' , 'fim-enquanto', :TYPE_REPETICAO  ),
(18, 'repita'   , 'ate'         , :TYPE_REPETICAO  ),
INSERT INTO language__bypass_command(bypass_command_id, language_id) VALUES
(10,2),
(11,2),
(12,2); --todo este está em verificação
(13,2);
(14,2);
(15,2);
(16,2);
(17,2);
(18,2);

