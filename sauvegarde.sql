--
-- PostgreSQL database dump
--

-- Dumped from database version 15.6
-- Dumped by pg_dump version 15.6

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
-- Name: roles; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.roles (
    idrole integer NOT NULL,
    nom character varying(100)
);


ALTER TABLE public.roles OWNER TO postgres;

--
-- Name: roles_idrole_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.roles_idrole_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.roles_idrole_seq OWNER TO postgres;

--
-- Name: roles_idrole_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.roles_idrole_seq OWNED BY public.roles.idrole;


--
-- Name: utilisateurs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.utilisateurs (
    idutilisateur integer NOT NULL,
    idrole integer,
    nom character varying(100),
    email character varying(200),
    motdepasse character varying(200)
);


ALTER TABLE public.utilisateurs OWNER TO postgres;

--
-- Name: utilisateurs_idutilisateur_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.utilisateurs_idutilisateur_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.utilisateurs_idutilisateur_seq OWNER TO postgres;

--
-- Name: utilisateurs_idutilisateur_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.utilisateurs_idutilisateur_seq OWNED BY public.utilisateurs.idutilisateur;


--
-- Name: roles idrole; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles ALTER COLUMN idrole SET DEFAULT nextval('public.roles_idrole_seq'::regclass);


--
-- Name: utilisateurs idutilisateur; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.utilisateurs ALTER COLUMN idutilisateur SET DEFAULT nextval('public.utilisateurs_idutilisateur_seq'::regclass);


--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.roles (idrole, nom) FROM stdin;
1	admin
2	membres
\.


--
-- Data for Name: utilisateurs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.utilisateurs (idutilisateur, idrole, nom, email, motdepasse) FROM stdin;
1	1	Jean Dupont	jean.dupont@email.com	MotDePasse123
2	1	Isaia Nirina	isaiajoellenirina@gmail.com	123
3	2	Toky oeuhfozef	tokyrajoelinjato@gmail.com	1234
5	1	Isaia Nirina	isaiajoea@gmail.com	$2y$10$8BgILPi5mUiKDNhR8sojTO50alHFDpZo3SNMvLEiaG9kWDHx2EQJ2
6	1	Isaia Nirina	isaiajoerrvfhyhtyna@gmail.com	$2y$10$d6nRbGtj9kPm.27gjKTeT.JH3V8hGCluGrEZYusmMW9WR8V1hklJu
7	1	Isaia Nirina	be!!!!@gmail.com	$2y$10$cM2NMgzmFS9G2Qo5md1MBeX8VnZc.AezYnxYi.oBZ2C8C574n2X1K
8	1	Isaia Nirina	isaasa@gmail.com	$2y$10$PfpAMWQoq/BooMZ5pmPEdeVNrDUAIpzsxsehIz7DOOsSg.LM4ynLG
9	1	Isaia Nirina	isaasa@gmail.com	$2y$10$WVlMYAgblQY0NyMbLLb6H.cBhG175eW48B4zjreSgQcci3XWO19Li
10	1	Isaia Nirina	isaasa@gmail.com	$2y$10$mzEVQy9JUQvnptY6hFSpJOQXrT6dIVhAiVBvC0YZqSCyaVV3H9jcy
11	1	Toky	toukyhrj@gmail.com	$2y$10$9BRshATb5oRrdv3wb7dzA.kespTAIDaYbz4HTbxMBgOAO0r/7fZiG
4	1	Isaia Joëlle	isaiajoellenirina@gmail.com	$2y$10$.nLXSRaPZY/2dOMJvTbeBe9NfV7ayLHpSLESkOB9UKYWRCUdDBedS
12	1	Mamitiana	mams.ratsimba@gmail.com	$2y$10$Zyc8CrORjef6M8b6MR9S.OArFGwerbxmmQM396VE4UKPgKvA.N9xC
13	1	Mamitiana	mams.ratsimba@gmail.com	$2y$10$t3FB8xSBBtvW1mlgqAqd5OyoTbCYqDkfwYwZjeC2jvxb8tQBJdbT.
\.


--
-- Name: roles_idrole_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.roles_idrole_seq', 2, true);


--
-- Name: utilisateurs_idutilisateur_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.utilisateurs_idutilisateur_seq', 13, true);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (idrole);


--
-- Name: utilisateurs utilisateurs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.utilisateurs
    ADD CONSTRAINT utilisateurs_pkey PRIMARY KEY (idutilisateur);


--
-- Name: utilisateurs utilisateurs_idrole_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.utilisateurs
    ADD CONSTRAINT utilisateurs_idrole_fkey FOREIGN KEY (idrole) REFERENCES public.roles(idrole);


--
-- PostgreSQL database dump complete
--

