--
-- PostgreSQL database dump
--

-- Dumped from database version 13.20
-- Dumped by pg_dump version 13.20

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: tests; Type: SCHEMA; Schema: -; Owner: student_test
--

CREATE SCHEMA tests;


ALTER SCHEMA tests OWNER TO student_test;

--
-- Name: pgcrypto; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS pgcrypto WITH SCHEMA public;


--
-- Name: EXTENSION pgcrypto; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION pgcrypto IS 'cryptographic functions';


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: answers; Type: TABLE; Schema: tests; Owner: student_test
--

CREATE TABLE tests.answers (
    id integer NOT NULL,
    result_id integer NOT NULL,
    question_id integer NOT NULL,
    option_id integer NOT NULL
);


ALTER TABLE tests.answers OWNER TO student_test;

--
-- Name: answers_id_seq; Type: SEQUENCE; Schema: tests; Owner: student_test
--

CREATE SEQUENCE tests.answers_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tests.answers_id_seq OWNER TO student_test;

--
-- Name: answers_id_seq; Type: SEQUENCE OWNED BY; Schema: tests; Owner: student_test
--

ALTER SEQUENCE tests.answers_id_seq OWNED BY tests.answers.id;


--
-- Name: options; Type: TABLE; Schema: tests; Owner: student_test
--

CREATE TABLE tests.options (
    id integer NOT NULL,
    question_id integer NOT NULL,
    option_text text NOT NULL,
    is_correct boolean DEFAULT false
);


ALTER TABLE tests.options OWNER TO student_test;

--
-- Name: options_id_seq; Type: SEQUENCE; Schema: tests; Owner: student_test
--

CREATE SEQUENCE tests.options_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tests.options_id_seq OWNER TO student_test;

--
-- Name: options_id_seq; Type: SEQUENCE OWNED BY; Schema: tests; Owner: student_test
--

ALTER SEQUENCE tests.options_id_seq OWNED BY tests.options.id;


--
-- Name: questions; Type: TABLE; Schema: tests; Owner: student_test
--

CREATE TABLE tests.questions (
    id integer NOT NULL,
    question_text text NOT NULL,
    question_type character varying(10) NOT NULL,
    created_by integer NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    exported boolean DEFAULT false
);


ALTER TABLE tests.questions OWNER TO student_test;

--
-- Name: questions_id_seq; Type: SEQUENCE; Schema: tests; Owner: student_test
--

CREATE SEQUENCE tests.questions_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tests.questions_id_seq OWNER TO student_test;

--
-- Name: questions_id_seq; Type: SEQUENCE OWNED BY; Schema: tests; Owner: student_test
--

ALTER SEQUENCE tests.questions_id_seq OWNED BY tests.questions.id;


--
-- Name: results; Type: TABLE; Schema: tests; Owner: student_test
--

CREATE TABLE tests.results (
    id integer NOT NULL,
    login_id integer NOT NULL,
    test_id integer NOT NULL,
    score integer NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE tests.results OWNER TO student_test;

--
-- Name: results_id_seq; Type: SEQUENCE; Schema: tests; Owner: student_test
--

CREATE SEQUENCE tests.results_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tests.results_id_seq OWNER TO student_test;

--
-- Name: results_id_seq; Type: SEQUENCE OWNED BY; Schema: tests; Owner: student_test
--

ALTER SEQUENCE tests.results_id_seq OWNED BY tests.results.id;


--
-- Name: student_logins; Type: TABLE; Schema: tests; Owner: student_test
--

CREATE TABLE tests.student_logins (
    id integer NOT NULL,
    login character varying(50) NOT NULL,
    password text NOT NULL,
    test_id integer NOT NULL
);


ALTER TABLE tests.student_logins OWNER TO student_test;

--
-- Name: student_logins_id_seq; Type: SEQUENCE; Schema: tests; Owner: student_test
--

CREATE SEQUENCE tests.student_logins_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tests.student_logins_id_seq OWNER TO student_test;

--
-- Name: student_logins_id_seq; Type: SEQUENCE OWNED BY; Schema: tests; Owner: student_test
--

ALTER SEQUENCE tests.student_logins_id_seq OWNED BY tests.student_logins.id;


--
-- Name: test_questions; Type: TABLE; Schema: tests; Owner: student_test
--

CREATE TABLE tests.test_questions (
    test_id integer NOT NULL,
    question_id integer NOT NULL
);


ALTER TABLE tests.test_questions OWNER TO student_test;

--
-- Name: tests; Type: TABLE; Schema: tests; Owner: student_test
--

CREATE TABLE tests.tests (
    id integer NOT NULL,
    test_name character varying(100) NOT NULL,
    created_by integer NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE tests.tests OWNER TO student_test;

--
-- Name: tests_id_seq; Type: SEQUENCE; Schema: tests; Owner: student_test
--

CREATE SEQUENCE tests.tests_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tests.tests_id_seq OWNER TO student_test;

--
-- Name: tests_id_seq; Type: SEQUENCE OWNED BY; Schema: tests; Owner: student_test
--

ALTER SEQUENCE tests.tests_id_seq OWNED BY tests.tests.id;


--
-- Name: text_answers; Type: TABLE; Schema: tests; Owner: student_test
--

CREATE TABLE tests.text_answers (
    id integer NOT NULL,
    question_id integer NOT NULL,
    correct_text text NOT NULL
);


ALTER TABLE tests.text_answers OWNER TO student_test;

--
-- Name: text_answers_id_seq; Type: SEQUENCE; Schema: tests; Owner: student_test
--

CREATE SEQUENCE tests.text_answers_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tests.text_answers_id_seq OWNER TO student_test;

--
-- Name: text_answers_id_seq; Type: SEQUENCE OWNED BY; Schema: tests; Owner: student_test
--

ALTER SEQUENCE tests.text_answers_id_seq OWNED BY tests.text_answers.id;


--
-- Name: text_user_answers; Type: TABLE; Schema: tests; Owner: student_test
--

CREATE TABLE tests.text_user_answers (
    result_id integer NOT NULL,
    question_id integer NOT NULL,
    student_text text
);


ALTER TABLE tests.text_user_answers OWNER TO student_test;

--
-- Name: users; Type: TABLE; Schema: tests; Owner: student_test
--

CREATE TABLE tests.users (
    id integer NOT NULL,
    login character varying(50) NOT NULL,
    password text NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE tests.users OWNER TO student_test;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: tests; Owner: student_test
--

CREATE SEQUENCE tests.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tests.users_id_seq OWNER TO student_test;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: tests; Owner: student_test
--

ALTER SEQUENCE tests.users_id_seq OWNED BY tests.users.id;


--
-- Name: answers id; Type: DEFAULT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.answers ALTER COLUMN id SET DEFAULT nextval('tests.answers_id_seq'::regclass);


--
-- Name: options id; Type: DEFAULT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.options ALTER COLUMN id SET DEFAULT nextval('tests.options_id_seq'::regclass);


--
-- Name: questions id; Type: DEFAULT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.questions ALTER COLUMN id SET DEFAULT nextval('tests.questions_id_seq'::regclass);


--
-- Name: results id; Type: DEFAULT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.results ALTER COLUMN id SET DEFAULT nextval('tests.results_id_seq'::regclass);


--
-- Name: student_logins id; Type: DEFAULT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.student_logins ALTER COLUMN id SET DEFAULT nextval('tests.student_logins_id_seq'::regclass);


--
-- Name: tests id; Type: DEFAULT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.tests ALTER COLUMN id SET DEFAULT nextval('tests.tests_id_seq'::regclass);


--
-- Name: text_answers id; Type: DEFAULT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.text_answers ALTER COLUMN id SET DEFAULT nextval('tests.text_answers_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.users ALTER COLUMN id SET DEFAULT nextval('tests.users_id_seq'::regclass);


--
-- Data for Name: answers; Type: TABLE DATA; Schema: tests; Owner: student_test
--

COPY tests.answers (id, result_id, question_id, option_id) FROM stdin;
97	13	89	160
98	13	90	165
99	13	91	168
100	13	92	174
101	13	93	176
102	13	94	180
103	13	94	182
104	13	95	184
105	13	95	185
106	13	95	186
107	13	96	188
108	13	96	190
109	13	97	192
110	13	97	194
111	13	98	196
112	13	98	197
113	13	104	201
114	13	105	204
115	13	105	205
116	13	105	206
117	14	89	160
118	14	90	164
119	14	91	168
120	14	92	174
121	14	93	177
122	14	94	180
123	14	95	184
124	14	95	185
125	14	96	188
126	14	96	190
127	14	97	192
128	14	97	194
129	14	98	196
130	14	98	197
131	14	98	199
132	14	104	200
133	14	105	204
134	14	105	205
135	14	105	206
\.


--
-- Data for Name: options; Type: TABLE DATA; Schema: tests; Owner: student_test
--

COPY tests.options (id, question_id, option_text, is_correct) FROM stdin;
160	89	Париж	t
161	89	Лондон	f
162	89	Берлин	f
163	89	Мадрид	f
164	90	3	f
165	90	4	t
166	90	5	f
167	90	22	f
168	91	Синий	t
169	91	Зеленый	f
170	91	Красный	f
171	91	Фиолетовый	f
172	92	5	f
173	92	6	f
174	92	7	t
175	92	8	f
176	93	Москва	t
177	93	Питер	f
178	93	Киев	f
179	93	Минск	f
180	94	Python	t
181	94	HTML	f
182	94	Java	t
183	94	CSS	f
184	95	Белый	t
185	95	Красный	t
186	95	Синий	t
187	95	Зеленый	f
188	96	Яблоко	t
189	96	Морковь	f
190	96	Банан	t
191	96	Капуста	f
192	97	Кошка	t
193	97	Акула	f
194	97	Собака	t
195	97	Черепаха	f
196	98	Chrome	t
197	98	Firefox	t
198	98	Excel	f
199	98	Edge	t
200	104	Python	f
201	104	JavaScript	t
202	104	Ruby	f
203	104	Java	f
204	105	Земля	t
205	105	Марс	t
206	105	Юпитер	t
207	105	Плутон	f
\.


--
-- Data for Name: questions; Type: TABLE DATA; Schema: tests; Owner: student_test
--

COPY tests.questions (id, question_text, question_type, created_by, created_at, exported) FROM stdin;
89	Столица Франции?	single	1	2025-04-16 14:23:17.82757	t
90	2 + 2 = ?	single	1	2025-04-16 14:23:17.838532	t
91	Цвет неба днем?	single	1	2025-04-16 14:23:17.842537	t
92	Сколько дней в неделе?	single	1	2025-04-16 14:23:17.846557	t
93	Столица России?	single	1	2025-04-16 14:23:17.850055	t
94	Выберите языки программирования	multiple	1	2025-04-16 14:23:17.853979	t
95	Выберите цвета флага РФ	multiple	1	2025-04-16 14:23:17.856689	t
96	Что относится к фруктам?	multiple	1	2025-04-16 14:23:17.858941	t
97	Какие животные млекопитающие?	multiple	1	2025-04-16 14:23:17.861785	t
98	Выберите браузеры	multiple	1	2025-04-16 14:23:17.86555	t
99	Какой язык ты изучаешь?	text	1	2025-04-16 14:23:17.868253	t
100	Год начала Второй мировой войны?	text	1	2025-04-16 14:23:17.87073	t
101	Название компании создателя Windows?	text	1	2025-04-16 14:23:17.871828	t
102	Сколько будет 10 в квадрате?	text	1	2025-04-16 14:23:17.872888	t
103	Что пьет корова?	text	1	2025-04-16 14:23:17.874372	t
104	Какой язык программирования используется для веб-разработки?	single	1	2025-04-16 14:41:41.690132	t
105	Назовите планеты солнечной системы	multiple	1	2025-04-16 14:41:41.703201	t
106	Какова формула углекислого газа?	text	1	2025-04-16 14:41:41.705723	t
\.


--
-- Data for Name: results; Type: TABLE DATA; Schema: tests; Owner: student_test
--

COPY tests.results (id, login_id, test_id, score, created_at) FROM stdin;
13	21	17	17	2025-04-16 15:07:44.360176
14	22	17	13	2025-04-16 15:23:10.872049
\.


--
-- Data for Name: student_logins; Type: TABLE DATA; Schema: tests; Owner: student_test
--

COPY tests.student_logins (id, login, password, test_id) FROM stdin;
21	stued20fd	$2y$12$DLLQHx2nz.yv9dT4XthrRu1BtTaHwhqbSINCh/Q.xRhmcoRSQ.eda	17
22	studb19ec	$2y$12$aZis4WPtG8.CVcaZqjRlwuqMMgaKcxE2ZuV8NCrXvQGC7LognGaQi	17
\.


--
-- Data for Name: test_questions; Type: TABLE DATA; Schema: tests; Owner: student_test
--

COPY tests.test_questions (test_id, question_id) FROM stdin;
17	89
17	90
17	91
17	92
17	93
17	94
17	95
17	96
17	97
17	98
17	99
17	100
17	101
17	102
17	103
17	104
17	105
17	106
\.


--
-- Data for Name: tests; Type: TABLE DATA; Schema: tests; Owner: student_test
--

COPY tests.tests (id, test_name, created_by, created_at) FROM stdin;
17	Тест	1	2025-04-16 15:05:36.034857
\.


--
-- Data for Name: text_answers; Type: TABLE DATA; Schema: tests; Owner: student_test
--

COPY tests.text_answers (id, question_id, correct_text) FROM stdin;
13	99	php
14	100	1939
15	101	microsoft
16	102	100
17	103	вода
18	106	CO2
\.


--
-- Data for Name: text_user_answers; Type: TABLE DATA; Schema: tests; Owner: student_test
--

COPY tests.text_user_answers (result_id, question_id, student_text) FROM stdin;
14	99	php
14	100	1939
14	101	microsoft
14	102	100
14	103	вода
14	106	CO2
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: tests; Owner: student_test
--

COPY tests.users (id, login, password, created_at) FROM stdin;
1	teacher1	$2y$12$toEKzCX9G.0yIyKO1vamsOt2Fay8upSNK6zIK6pZsm0zI4nz.pBHC	2025-04-02 01:24:39.953439
\.


--
-- Name: answers_id_seq; Type: SEQUENCE SET; Schema: tests; Owner: student_test
--

SELECT pg_catalog.setval('tests.answers_id_seq', 135, true);


--
-- Name: options_id_seq; Type: SEQUENCE SET; Schema: tests; Owner: student_test
--

SELECT pg_catalog.setval('tests.options_id_seq', 215, true);


--
-- Name: questions_id_seq; Type: SEQUENCE SET; Schema: tests; Owner: student_test
--

SELECT pg_catalog.setval('tests.questions_id_seq', 109, true);


--
-- Name: results_id_seq; Type: SEQUENCE SET; Schema: tests; Owner: student_test
--

SELECT pg_catalog.setval('tests.results_id_seq', 14, true);


--
-- Name: student_logins_id_seq; Type: SEQUENCE SET; Schema: tests; Owner: student_test
--

SELECT pg_catalog.setval('tests.student_logins_id_seq', 22, true);


--
-- Name: tests_id_seq; Type: SEQUENCE SET; Schema: tests; Owner: student_test
--

SELECT pg_catalog.setval('tests.tests_id_seq', 20, true);


--
-- Name: text_answers_id_seq; Type: SEQUENCE SET; Schema: tests; Owner: student_test
--

SELECT pg_catalog.setval('tests.text_answers_id_seq', 19, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: tests; Owner: student_test
--

SELECT pg_catalog.setval('tests.users_id_seq', 1, true);


--
-- Name: answers answers_pkey; Type: CONSTRAINT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.answers
    ADD CONSTRAINT answers_pkey PRIMARY KEY (id);


--
-- Name: options options_pkey; Type: CONSTRAINT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.options
    ADD CONSTRAINT options_pkey PRIMARY KEY (id);


--
-- Name: questions questions_pkey; Type: CONSTRAINT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.questions
    ADD CONSTRAINT questions_pkey PRIMARY KEY (id);


--
-- Name: results results_pkey; Type: CONSTRAINT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.results
    ADD CONSTRAINT results_pkey PRIMARY KEY (id);


--
-- Name: student_logins student_logins_login_key; Type: CONSTRAINT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.student_logins
    ADD CONSTRAINT student_logins_login_key UNIQUE (login);


--
-- Name: student_logins student_logins_pkey; Type: CONSTRAINT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.student_logins
    ADD CONSTRAINT student_logins_pkey PRIMARY KEY (id);


--
-- Name: test_questions test_questions_pkey; Type: CONSTRAINT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.test_questions
    ADD CONSTRAINT test_questions_pkey PRIMARY KEY (test_id, question_id);


--
-- Name: tests tests_pkey; Type: CONSTRAINT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.tests
    ADD CONSTRAINT tests_pkey PRIMARY KEY (id);


--
-- Name: text_answers text_answers_pkey; Type: CONSTRAINT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.text_answers
    ADD CONSTRAINT text_answers_pkey PRIMARY KEY (id);


--
-- Name: text_user_answers text_user_answers_pkey; Type: CONSTRAINT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.text_user_answers
    ADD CONSTRAINT text_user_answers_pkey PRIMARY KEY (result_id, question_id);


--
-- Name: questions unique_question_text; Type: CONSTRAINT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.questions
    ADD CONSTRAINT unique_question_text UNIQUE (question_text);


--
-- Name: users users_login_key; Type: CONSTRAINT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.users
    ADD CONSTRAINT users_login_key UNIQUE (login);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: answers answers_option_id_fkey; Type: FK CONSTRAINT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.answers
    ADD CONSTRAINT answers_option_id_fkey FOREIGN KEY (option_id) REFERENCES tests.options(id) ON DELETE CASCADE;


--
-- Name: answers answers_question_id_fkey; Type: FK CONSTRAINT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.answers
    ADD CONSTRAINT answers_question_id_fkey FOREIGN KEY (question_id) REFERENCES tests.questions(id) ON DELETE CASCADE;


--
-- Name: answers answers_result_id_fkey; Type: FK CONSTRAINT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.answers
    ADD CONSTRAINT answers_result_id_fkey FOREIGN KEY (result_id) REFERENCES tests.results(id) ON DELETE CASCADE;


--
-- Name: options options_question_id_fkey; Type: FK CONSTRAINT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.options
    ADD CONSTRAINT options_question_id_fkey FOREIGN KEY (question_id) REFERENCES tests.questions(id) ON DELETE CASCADE;


--
-- Name: questions questions_created_by_fkey; Type: FK CONSTRAINT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.questions
    ADD CONSTRAINT questions_created_by_fkey FOREIGN KEY (created_by) REFERENCES tests.users(id);


--
-- Name: results results_login_id_fkey; Type: FK CONSTRAINT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.results
    ADD CONSTRAINT results_login_id_fkey FOREIGN KEY (login_id) REFERENCES tests.student_logins(id) ON DELETE CASCADE;


--
-- Name: results results_test_id_fkey; Type: FK CONSTRAINT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.results
    ADD CONSTRAINT results_test_id_fkey FOREIGN KEY (test_id) REFERENCES tests.tests(id) ON DELETE CASCADE;


--
-- Name: student_logins student_logins_test_id_fkey; Type: FK CONSTRAINT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.student_logins
    ADD CONSTRAINT student_logins_test_id_fkey FOREIGN KEY (test_id) REFERENCES tests.tests(id);


--
-- Name: test_questions test_questions_question_id_fkey; Type: FK CONSTRAINT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.test_questions
    ADD CONSTRAINT test_questions_question_id_fkey FOREIGN KEY (question_id) REFERENCES tests.questions(id) ON DELETE CASCADE;


--
-- Name: test_questions test_questions_test_id_fkey; Type: FK CONSTRAINT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.test_questions
    ADD CONSTRAINT test_questions_test_id_fkey FOREIGN KEY (test_id) REFERENCES tests.tests(id) ON DELETE CASCADE;


--
-- Name: tests tests_created_by_fkey; Type: FK CONSTRAINT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.tests
    ADD CONSTRAINT tests_created_by_fkey FOREIGN KEY (created_by) REFERENCES tests.users(id);


--
-- Name: text_answers text_answers_question_id_fkey; Type: FK CONSTRAINT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.text_answers
    ADD CONSTRAINT text_answers_question_id_fkey FOREIGN KEY (question_id) REFERENCES tests.questions(id) ON DELETE CASCADE;


--
-- Name: text_user_answers text_user_answers_question_id_fkey; Type: FK CONSTRAINT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.text_user_answers
    ADD CONSTRAINT text_user_answers_question_id_fkey FOREIGN KEY (question_id) REFERENCES tests.questions(id) ON DELETE CASCADE;


--
-- Name: text_user_answers text_user_answers_result_id_fkey; Type: FK CONSTRAINT; Schema: tests; Owner: student_test
--

ALTER TABLE ONLY tests.text_user_answers
    ADD CONSTRAINT text_user_answers_result_id_fkey FOREIGN KEY (result_id) REFERENCES tests.results(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

