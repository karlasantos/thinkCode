-- noinspection SqlNoDataSourceInspectionForFile

-- Insere as Linguagens
INSERT INTO languages(id, name, initial_code_structure, end_code_structure, initial_vertex_name, end_vertex_name, if_then_name_vertex)
VALUES (1, 'Linguagem C', 'int main()|int main ()', '}', 'start', 'end', 'then'), (2, 'Portugol', 'inicio', 'fim', 'inicio', 'fim', 'entao');

-- Insere os tipos de dados
INSERT INTO data_types(id, name, byte_size) VALUES
(1, 'char'                , 1),
(2, 'unsigned char'       , 1),
(3, 'short'               , 2),
(4, 'unsigned short'      , 2),
(5, 'int'                 , 4),
(6, 'unsigned int'        , 4),
(7, 'long'                , 4),
(8, 'float'               , 8),
(9, 'double'              , 8),
(10, 'signed long long'   , 8),
(11, 'unsigned long long' , 8),
(12, 'real'               , 8), -- tamanho com base nos da linguagem C
(13, 'inteiro'            , 4), -- tamanho com base nos da linguagem C
(14, 'cadeia'             , 8), -- tamanho com base nos da linguagem C
(15, 'caracter'           , 1), -- tamanho com base nos da linguagem C
(16, 'logico'             , 2); -- tamanho com base nos da linguagem C

-- Insere o relacionamento entre os tipos de dados e as linguagens de programação
INSERT INTO language__data_type(data_type_id, language_id) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10,1),
(11,1),
(12,2),
(13,2),
(14,2),
(15,2),
(16,2);

INSERT INTO special_characters(id, name) VALUES
(1,'('),
(2,')'),
(3,';'),
(4,'='),
(5,'+'),
(6,'-'),
(7,'*'),
(8,'/'),
(9,'%'),
(10,'=='),
(11,'>'),
(12,'<'),
(13,'!='),
(14,'#'),
(15,'{'),
(16,'}'),
(17,':'),
(18,'^');

-- RELACIONAMENTOS DA LINGUAGEM C
INSERT INTO language__special_character(special_character_id, language_id) VALUES
(1,1),
(2,1),
(3,1),
(4,1),
(5,1),
(6,1),
(7,1),
(8,1),
(9,1),
(10,1),
(11,1),
(12,1),
(13,1),
(14,1),
(15,1),
(16,1),
(17,1),
(1,2),
(2,2),
(4,2),
(5,2),
(6,2),
(7,2),
(8,2),
(9,2),
(11,2),
(12,2),
(18,2);


-- Insere os conectivos lógicos da Linguagem C e cria os relacionamentos
INSERT INTO logical_connectives(id, name) VALUES (1, '&&'), (2, '||'), (3, '!');
INSERT INTO language__logical_connective(logical_connective_id, language_id) VALUES (1,1), (2,1), (3,1);

-- Insere os conectivos lógicos do Portugol e cria os relacionamentos
INSERT INTO logical_connectives(id, name) VALUES (4, 'e'), (5, 'ou'), (6, 'nao'), (7, 'xou');
INSERT INTO language__logical_connective(logical_connective_id, language_id) VALUES (4,2), (5,2), (6,2), (7,2);

-- Insere os elementos gráficos de cada estrutura de desvio
INSERT INTO graph_elements(id, name, type) VALUES
(1, 'if'         , 'conditional'),
(2, 'else-if'    , 'conditional'),
(3, 'if-else'    , 'conditional'),
(4, 'switch-case', 'conditional'),
(5, 'for'        , 'loop'),
(6, 'while'      , 'loop'),
(7, 'do-while'   , 'loop');

-- Insere os comandos de desvio da Linguagem C e cria os relacionamentos
INSERT INTO diversion_commands(id, initial_command_name, terminal_command_name, type, graph_element_id) VALUES
(1, 'if'     , '}'             , 'conditional', 1),
(2, 'else'   , '}'             , 'conditional', 3),
(3, 'elseif' , '}'             , 'conditional', 2),
(4, 'switch' , '}'             , 'conditional', 4),
(5, 'case'   , 'case|default|}', 'conditional', 4),
(6, 'default', '}|$'           , 'conditional', 4),
(7, 'for'    , '}'             , 'loop'       , 5),
(8, 'while'  , '}'             , 'loop'       , 6),
(9, 'do'     , 'while'         , 'loop'         , 7);
INSERT INTO language__bypass_command(bypass_command_id, language_id) VALUES
(1,1),
(2,1),
(3,1),
(4,1),
(5,1),
(6,1),
(7,1),
(8,1),
(9,1);

-- Insere os comandos do Portugol e cria os relacionamentos
INSERT INTO diversion_commands(id, initial_command_name, terminal_command_name, type, graph_element_id) VALUES
(10, 'se'       , 'senao|fimse'            , 'conditional', 1),
(11, 'senao'    , 'fimse'                  , 'conditional', 3), -- todo verificar e colocar o senaose
(12, 'escolhe'  , 'fimescolhe'             , 'conditional', 4),
(13, 'caso'     , 'caso|defeito|fimescolhe', 'conditional', 4),
(14, 'defeito'  , 'fimescolhe|$'           , 'conditional', 4),
(15, 'para'     , 'fimpara'                , 'loop'       , 5),
(16, 'enquanto' , 'fimenquanto'            , 'loop'       , 6),
(17, 'repete'   , 'ate'                    , 'loop'       , 7),
INSERT INTO language__bypass_command(bypass_command_id, language_id) VALUES
(10,2),
(11,2),
(12,2),
(13,2),
(14,2),
(15,2),
(16,2),
(17,2);