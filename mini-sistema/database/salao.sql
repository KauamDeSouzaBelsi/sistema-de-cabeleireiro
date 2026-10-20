--
-- PostgreSQL database dump
--

\restrict 2zsb7rSFDvQ6cOuUjVS94p6MQm2rN4qf2NCvzkA6f588duOIkcuDthipC2sG5er

-- Dumped from database version 18.4 (Ubuntu 18.4-0ubuntu0.26.04.1)
-- Dumped by pg_dump version 18.4 (Ubuntu 18.4-0ubuntu0.26.04.1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: administradores; Type: TABLE; Schema: public; Owner: salao
--

CREATE TABLE public.administradores (
    id integer NOT NULL,
    nome character varying(100) NOT NULL,
    email character varying(100) NOT NULL,
    senha character varying(255) NOT NULL
);


ALTER TABLE public.administradores OWNER TO salao;

--
-- Name: administradores_id_seq; Type: SEQUENCE; Schema: public; Owner: salao
--

CREATE SEQUENCE public.administradores_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.administradores_id_seq OWNER TO salao;

--
-- Name: administradores_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: salao
--

ALTER SEQUENCE public.administradores_id_seq OWNED BY public.administradores.id;


--
-- Name: agendamentos; Type: TABLE; Schema: public; Owner: salao
--

CREATE TABLE public.agendamentos (
    id integer NOT NULL,
    cliente_id integer NOT NULL,
    corte_id integer NOT NULL,
    data date NOT NULL,
    horario time without time zone NOT NULL,
    observacoes character varying(500),
    status character varying(20) DEFAULT 'Pendente'::character varying NOT NULL
);


ALTER TABLE public.agendamentos OWNER TO salao;

--
-- Name: agendamentos_id_seq; Type: SEQUENCE; Schema: public; Owner: salao
--

CREATE SEQUENCE public.agendamentos_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.agendamentos_id_seq OWNER TO salao;

--
-- Name: agendamentos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: salao
--

ALTER SEQUENCE public.agendamentos_id_seq OWNED BY public.agendamentos.id;


--
-- Name: cortes; Type: TABLE; Schema: public; Owner: salao
--

CREATE TABLE public.cortes (
    id integer NOT NULL,
    nome character varying(100) NOT NULL,
    preco numeric(10,2) NOT NULL,
    descricao text
);


ALTER TABLE public.cortes OWNER TO salao;

--
-- Name: cortes_id_seq; Type: SEQUENCE; Schema: public; Owner: salao
--

CREATE SEQUENCE public.cortes_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.cortes_id_seq OWNER TO salao;

--
-- Name: cortes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: salao
--

ALTER SEQUENCE public.cortes_id_seq OWNED BY public.cortes.id;


--
-- Name: estoque; Type: TABLE; Schema: public; Owner: salao
--

CREATE TABLE public.estoque (
    id integer NOT NULL,
    nome character varying(100) NOT NULL,
    categoria character varying(50) NOT NULL,
    fornecedor character varying(100) NOT NULL,
    quantidade integer NOT NULL,
    preco_unitario numeric(10,2) NOT NULL
);


ALTER TABLE public.estoque OWNER TO salao;

--
-- Name: estoque_id_seq; Type: SEQUENCE; Schema: public; Owner: salao
--

CREATE SEQUENCE public.estoque_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.estoque_id_seq OWNER TO salao;

--
-- Name: estoque_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: salao
--

ALTER SEQUENCE public.estoque_id_seq OWNED BY public.estoque.id;


--
-- Name: usuarios; Type: TABLE; Schema: public; Owner: salao
--

CREATE TABLE public.usuarios (
    id integer NOT NULL,
    nome character varying(100) NOT NULL,
    email character varying(100) NOT NULL,
    senha character varying(255) NOT NULL
);


ALTER TABLE public.usuarios OWNER TO salao;

--
-- Name: usuarios_id_seq; Type: SEQUENCE; Schema: public; Owner: salao
--

CREATE SEQUENCE public.usuarios_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.usuarios_id_seq OWNER TO salao;

--
-- Name: usuarios_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: salao
--

ALTER SEQUENCE public.usuarios_id_seq OWNED BY public.usuarios.id;


--
-- Name: administradores id; Type: DEFAULT; Schema: public; Owner: salao
--

ALTER TABLE ONLY public.administradores ALTER COLUMN id SET DEFAULT nextval('public.administradores_id_seq'::regclass);


--
-- Name: agendamentos id; Type: DEFAULT; Schema: public; Owner: salao
--

ALTER TABLE ONLY public.agendamentos ALTER COLUMN id SET DEFAULT nextval('public.agendamentos_id_seq'::regclass);


--
-- Name: cortes id; Type: DEFAULT; Schema: public; Owner: salao
--

ALTER TABLE ONLY public.cortes ALTER COLUMN id SET DEFAULT nextval('public.cortes_id_seq'::regclass);


--
-- Name: estoque id; Type: DEFAULT; Schema: public; Owner: salao
--

ALTER TABLE ONLY public.estoque ALTER COLUMN id SET DEFAULT nextval('public.estoque_id_seq'::regclass);


--
-- Name: usuarios id; Type: DEFAULT; Schema: public; Owner: salao
--

ALTER TABLE ONLY public.usuarios ALTER COLUMN id SET DEFAULT nextval('public.usuarios_id_seq'::regclass);


--
-- Name: administradores administradores_email_key; Type: CONSTRAINT; Schema: public; Owner: salao
--

ALTER TABLE ONLY public.administradores
    ADD CONSTRAINT administradores_email_key UNIQUE (email);


--
-- Name: administradores administradores_pkey; Type: CONSTRAINT; Schema: public; Owner: salao
--

ALTER TABLE ONLY public.administradores
    ADD CONSTRAINT administradores_pkey PRIMARY KEY (id);


--
-- Name: agendamentos agendamentos_pkey; Type: CONSTRAINT; Schema: public; Owner: salao
--

ALTER TABLE ONLY public.agendamentos
    ADD CONSTRAINT agendamentos_pkey PRIMARY KEY (id);


--
-- Name: cortes cortes_pkey; Type: CONSTRAINT; Schema: public; Owner: salao
--

ALTER TABLE ONLY public.cortes
    ADD CONSTRAINT cortes_pkey PRIMARY KEY (id);


--
-- Name: estoque estoque_pkey; Type: CONSTRAINT; Schema: public; Owner: salao
--

ALTER TABLE ONLY public.estoque
    ADD CONSTRAINT estoque_pkey PRIMARY KEY (id);


--
-- Name: usuarios usuarios_email_key; Type: CONSTRAINT; Schema: public; Owner: salao
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_email_key UNIQUE (email);


--
-- Name: usuarios usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: salao
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_pkey PRIMARY KEY (id);


--
-- Name: agendamentos agendamentos_cliente_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: salao
--

ALTER TABLE ONLY public.agendamentos
    ADD CONSTRAINT agendamentos_cliente_id_fkey FOREIGN KEY (cliente_id) REFERENCES public.usuarios(id);


--
-- Name: agendamentos agendamentos_corte_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: salao
--

ALTER TABLE ONLY public.agendamentos
    ADD CONSTRAINT agendamentos_corte_id_fkey FOREIGN KEY (corte_id) REFERENCES public.cortes(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict 2zsb7rSFDvQ6cOuUjVS94p6MQm2rN4qf2NCvzkA6f588duOIkcuDthipC2sG5er

