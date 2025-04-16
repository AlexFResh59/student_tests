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

CREATE EXTENSION IF NOT EXISTS pgcrypto WITH SCHEMA tests;


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
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
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
\.


--
-- Data for Name: options; Type: TABLE DATA; Schema: tests; Owner: student_test
--

COPY tests.options (id, question_id, option_text, is_correct) FROM stdin;
\.


--
-- Data for Name: questions; Type: TABLE DATA; Schema: tests; Owner: student_test
--

COPY tests.questions (id, question_text, question_type, created_by, created_at) FROM stdin;
\.


--
-- Data for Name: results; Type: TABLE DATA; Schema: tests; Owner: student_test
--

COPY tests.results (id, login_id, test_id, score, created_at) FROM stdin;
\.


--
-- Data for Name: student_logins; Type: TABLE DATA; Schema: tests; Owner: student_test
--

COPY tests.student_logins (id, login, password, test_id) FROM stdin;
\.


--
-- Data for Name: test_questions; Type: TABLE DATA; Schema: tests; Owner: student_test
--

COPY tests.test_questions (test_id, question_id) FROM stdin;
\.


--
-- Data for Name: tests; Type: TABLE DATA; Schema: tests; Owner: student_test
--

COPY tests.tests (id, test_name, created_by, created_at) FROM stdin;
\.


--
-- Data for Name: text_answers; Type: TABLE DATA; Schema: tests; Owner: student_test
--

COPY tests.text_answers (id, question_id, correct_text) FROM stdin;
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

SELECT pg_catalog.setval('tests.answers_id_seq', 23, true);


--
-- Name: options_id_seq; Type: SEQUENCE SET; Schema: tests; Owner: student_test
--

SELECT pg_catalog.setval('tests.options_id_seq', 39, true);


--
-- Name: questions_id_seq; Type: SEQUENCE SET; Schema: tests; Owner: student_test
--

SELECT pg_catalog.setval('tests.questions_id_seq', 13, true);


--
-- Name: results_id_seq; Type: SEQUENCE SET; Schema: tests; Owner: student_test
--

SELECT pg_catalog.setval('tests.results_id_seq', 7, true);


--
-- Name: student_logins_id_seq; Type: SEQUENCE SET; Schema: tests; Owner: student_test
--

SELECT pg_catalog.setval('tests.student_logins_id_seq', 10, true);


--
-- Name: tests_id_seq; Type: SEQUENCE SET; Schema: tests; Owner: student_test
--

SELECT pg_catalog.setval('tests.tests_id_seq', 4, true);


--
-- Name: text_answers_id_seq; Type: SEQUENCE SET; Schema: tests; Owner: student_test
--

SELECT pg_catalog.setval('tests.text_answers_id_seq', 2, true);


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
-- PostgreSQL database dump complete
--

