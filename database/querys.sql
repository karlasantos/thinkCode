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

-- Categorias dos problemas
INSERT INTO categories_problem(id, name, description) VALUES
(1, 'Iniciante', 'Problemas de nível inicial.'),
(2, 'Intermediário', 'Problemas de nível intermediário.'),
(3, 'Difícil', 'Problemas de nível difícil.');

-- Problemas
INSERT INTO problems(id, category_id, title, description) VALUES
( 1, 1, 'Fatorial', 'Desenvolva um programa que calcule e mostre o fatorial de um número inteiro informado.'),
( 2, 1, 'Altura de Zé e Chico', 'Chico tem 1,50 metro e cresce 2 centímetros por ano, enquanto Zé tem 1,10 metro e cresce 3 centímetros por ano. Construa um algoritmo que calcule e imprima quantos anos serão necessários para que Zé seja maior que Chico.'),
( 3, 1, 'Categoria do Nadador', 'Elabore um programa que dada a idade de um nadador classifica-o em uma das seguintes categorias:
- infantil A = 5 - 7 anos
- infantil B = 8-10 anos
- juvenil A = 11-13 anos
- juvenil B = 14-17 anos
- adulto = maiores de 18 anos'),
( 4, 1, 'Número Primo', 'Faça um algoritmo que leia um número inteiro e logo após mostre uma mensagem se ele é primo.'),
( 5, 1, 'Número Perfeito', 'Um número inteiro é dito perfeito se o dobro dele é igual à soma de todos os seus divisores. Por exemplo, como os divisores de 6 são 1, 2, 3 e 6 e 1+2+3+6=12, 6 é perfeito. Escreva um programa que liste todos os números perfeitos menores que um inteiro n dado.'),
( 6, 1, 'Maior e Menor', 'Escreva um algoritmo que leia 4 números inteiros e mostre o maior e o menor valor informado.'),
( 7, 1, 'Valor da chamada', 'Uma companhia telefônica opera com a seguinte tarifa: uma chamada telefônica com duração de 3 minutos custa R$ 1.15. Cada minuto adicional custa R$ 0.26. Escreva um programa que leia a duração total de uma chamada (em minutos) e calcule o total a ser pago.'),
( 8, 1, 'Fibonacci', 'Escreva um programa que imprime na tela a série de FIBONACCI até um numero dado. Esta séria começa com 1 e 1 e os próximos números são obtidos pela soma dos anteriores. Ex: 1 1 2 3 5 8 13 21 34 55'),
( 9, 2, 'Seleção', 'Entrar com nome, idade e sexo de 5 pessoas, e imprimir na tela o nome dos homens maiores de 21 anos.'),
( 10, 2, 'Lanchonete', 'Uma lanchonete possui o seguinte cardápio:
Código Produto Valor (R$)
1 Refrigerante 2,50
2 Bolo 3,50
3 Torrada 4,50
4 Picadinho 10,00
5 Agua mineral 2,00
Faça um programa que controle a venda destes produtos. Um cliente deve ser capaz de comprar quantos produtos ele desejar. O programa deve solicitar o código do produto e quantidade. A compra deve ser finalizada quando for digitado o valor 99. Nesse momento deve ser mostrado o valor total que o cliente deve pagar. Simule a situação onde um cliente compra 2 refrigerantes + 1 picadinho + 1 bolo (total a pagar = R$ 18,50).'),
(11, 2, 'Faixas etárias', 'Escreva um algoritmo que receba a idade de 15 pessoas, calcule e escreva:

a)     a quantidade de pessoas em cada faixa;
b)     a porcentagem de cada faixa etária em relação ao total de pessoas;

As faixas etárias são:
 1 --------- 15 anos
16 -------  30 anos
31 -------  45 anos
> 45 anos'),
(12, 2, 'Classificação de Números', 'Faça um algoritmo que leia 10 valores inteiros. A seguir mostre o total de números pares, total de números ímpares, total de positivos, total de negativos e o total de zeros.'),
(13, 2, 'União de Vetores', 'Faça um algoritmo que leia 2(dois) vetores A e B de tamanho 10 de inteiros. Logo após deverá ser criado e exibido um vetor C de tamanho 20, que contenha todos os elementos dos vetores  A e B.'),
(14, 2, 'Dois Maiores Números do Vetor', 'Faça um algoritmo que leia um vetor de números inteiros de tamanho 20. Logo após, calcule e mostre dois maiores números armazenados no vetor lido.'),
(15, 3, 'Pesquisa do Cinema', 'Um cinema possui capacidade de 100 lugares e está sempre com ocupação total. Certo dia, cada espectador respondeu a um questionário, no qual constava:
- sua idade;
- sua opinião em relação ao filme, segundo as seguintes notas:
Nota   Significado
A       Ótimo
B       Bom
C       Regular
D       Ruim
E       Péssimo

Elabore um algoritmo que, lendo esses dados, calcule e imprima:
a)    a quantidade de respostas Ótimo;
b)    o total de respostas consideradas Bom e Regular;
c)    a média de idade das pessoas que responderam Ruim;
d)    o percentual de respostas Péssimo e a maior idade que escolheu essa opção;
e)    a diferença de idade entre a maior idade que respondeu Ótimo e a maior idade que respondeu Ruim;
');


-- SELECT DE SELEÇÃO DOS DADOS PARA A ANÁLISE
SELECT
  users.id as user_id,
  users.created as user_created,
  pf.school as user_school,
  sc.problem_id,
  rank.ranking,
  ar.arithmetic_mean,
  ar.number_useful_lines,
  ar.number_variables,
  ar.number_logical_connectives,
  ar.number_diversion_coommands,
  ar.number_regions_graph,
  ar.number_edges_graph,
  ar.number_vertex_graph,
  ar.cyclomatic_complexity
FROM analysis_results ar
LEFT JOIN source_codes sc ON sc.analysis_results_id = ar.id
LEFT JOIN users ON users.id = sc.user_id
LEFT JOIN profiles pf ON users.profile_id = pf.id
LEFT JOIN rank ON rank.source_code_id = sc.id
ORDER BY problem_id ASC, ranking ASC;

-- SELECT DE SELEÇÃO DOS DADOS PARA A ANÁLISE CORRETO
SELECT
  users.id as user_id,
  users.created as user_created,
  pf.school as user_school,
  sc.problem_id,
  rank.ranking,
  ar.mean,
  ar.number_useful_lines,
  ar.number_variables,
  ar.number_logical_connectives,
  ar.number_diversion_commands,
  ar.number_regions_graph,
  ar.number_edges_graph,
  ar.number_vertex_graph,
  ar.cyclomatic_complexity
FROM analysis_results ar
LEFT JOIN source_codes sc ON sc.analysis_results_id = ar.id
LEFT JOIN users ON users.id = sc.user_id
LEFT JOIN profiles pf ON users.profile_id = pf.id
LEFT JOIN rank ON rank.source_code_id = sc.id
ORDER BY problem_id ASC, ranking ASC;