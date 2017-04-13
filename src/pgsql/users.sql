-- Table: users

-- DROP TABLE users;

CREATE TABLE users
(
  email           text NOT NULL,
  password        text,
  auth_code       text,
  verified        integer,
  channel_name    text,
  channel_title   text,
  stream_key      character(10),
  display_name    text,
  profile_img     text,
  api_key         text,
  chat_jp_setting TEXT,
  is_admin        BOOLEAN,
  CONSTRAINT plk_email PRIMARY KEY (email)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE users
  OWNER TO pg_sql_account_here;
COMMENT ON TABLE users
  IS 'Table for managing user information/data';
