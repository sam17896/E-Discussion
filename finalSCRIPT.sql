DROP TABLE ThreadMessage;
DROP TABLE Messagenot;
DROP TABLE UsersFriend;
DROP TABLE phonenumber;
DROP TABLE Thread;
DROP TABLE Groupactivity;
DROP TABLE permission;
DROP TABLE Friendship;
DROP TABLE TopicMessage;
DROP TABLE TopicUsers;
DROP TABLE UsersLinks;
DROP TABLE Message;
DROP TABLE Notification;
DROP TABLE Usersskill;
DROP TABLE Activity;
DROP TABLE Education;
DROP TABLE Work;
DROP TABLE Userdetails;
DROP TABLE Tags;
DROP TABLE topiccategory;
DROP TABLE Topic;
DROP TABLE Usersinterest;
DROP TABLE numbertype;
DROP TABLE Links;
DROP TABLE Users;
CREATE SEQUENCE GlobalSequence;
DROP SEQUENCE user_seq;
DROP SEQUENCE act_seq;
DROP SEQUENCE friend_seq;
DROP SEQUENCE gr_act;
DROP SEQUENCE msg_seq;
DROP SEQUENCE msgnot_seq;
DROP SEQUENCE thread_seq;
DROP SEQUENCE topic_seq;
DROP SEQUENCE not_seq;
create sequence user_seq start with 100 increment by 1;
create sequence topic_seq start with 200 increment by 1;
create sequence act_seq start with 300 increment by 1;
create sequence not_seq start with 500 increment by 1;
create sequence msg_seq start with 600 increment by 1;
create sequence friend_seq start with 700 increment by 1;
create sequence gr_act start with 800 increment by 1;
create sequence thread_seq start with 900 increment by 1;
create sequence msgnot_seq start with 1000 increment by 1;



CREATE TABLE Users (
  UsersId INTEGER   NOT NULL ,
  Username VARCHAR(40)   NOT NULL ,
  pass VARCHAR(40)   NOT NULL ,
  emailid VARCHAR(255)   NOT NULL ,
  timeofregistration TIMESTAMP   NOT NULL ,
  status_2 INTEGER  DEFAULT 0 NOT NULL ,
  reference VARCHAR(40)    ,
  line INTEGER  DEFAULT 0  ,
  userpic VARCHAR(100)    ,
  usercover VARCHAR(100)      ,
PRIMARY KEY(UsersId));


CREATE OR REPLACE TRIGGER AINC_Users
BEFORE INSERT  ON Users
FOR EACH ROW 
BEGIN 
  IF (:NEW.UsersId IS NULL) THEN 
    SELECT GlobalSequence.NEXTVAL INTO :NEW.UsersId FROM DUAL; 
  END IF; 
END; 

/




CREATE TABLE Links (
  id INTEGER   NOT NULL ,
  name VARCHAR(255)      ,
PRIMARY KEY(id));


CREATE OR REPLACE TRIGGER AINC_Links
BEFORE INSERT  ON Links
FOR EACH ROW 
BEGIN 
  IF (:NEW.id IS NULL) THEN 
    SELECT GlobalSequence.NEXTVAL INTO :NEW.id FROM DUAL; 
  END IF; 
END; 
/

CREATE TABLE numbertype (
  typeid INTEGER   NOT NULL ,
  typename VARCHAR(20)   NOT NULL   ,
PRIMARY KEY(typeid));


CREATE OR REPLACE TRIGGER AINC_numbertype
BEFORE INSERT  ON numbertype
FOR EACH ROW 
BEGIN 
  IF (:NEW.typeid IS NULL) THEN 
    SELECT GlobalSequence.NEXTVAL INTO :NEW.typeid FROM DUAL; 
  END IF; 
END; 
/

CREATE TABLE Usersinterest (
  UsersId INTEGER   NOT NULL ,
  intrest VARCHAR(200)   NOT NULL   ,
PRIMARY KEY(UsersId, intrest)  ,
  FOREIGN KEY(UsersId)
    REFERENCES Users(UsersId));


CREATE INDEX Users_has_Interest_FKIndex1 ON Usersinterest (UsersId);


CREATE INDEX IFK_has ON Usersinterest (UsersId);


CREATE TABLE Topic (
  id INTEGER   NOT NULL ,
  adminid INTEGER   NOT NULL ,
  name VARCHAR(255)   NOT NULL ,
  description VARCHAR(255)    ,
  time TIMESTAMP   NOT NULL   ,
PRIMARY KEY(id)  ,
  FOREIGN KEY(adminid)
    REFERENCES Users(UsersId));


CREATE INDEX Topic_FKIndex1 ON Topic (adminid);


CREATE OR REPLACE TRIGGER AINC_Topic
BEFORE INSERT  ON Topic
FOR EACH ROW 
BEGIN 
  IF (:NEW.id IS NULL) THEN 
    SELECT GlobalSequence.NEXTVAL INTO :NEW.id FROM DUAL; 
  END IF; 
END; 

/


CREATE INDEX IFK_create ON Topic (adminid);


CREATE TABLE topiccategory (
  Topic_id INTEGER   NOT NULL ,
  category VARCHAR(255)   NOT NULL   ,
PRIMARY KEY(Topic_id, category)  ,
  FOREIGN KEY(Topic_id)
    REFERENCES Topic(id));


CREATE INDEX category_has_Topic_FKIndex2 ON topiccategory (Topic_id);


CREATE INDEX IFK_has ON topiccategory (Topic_id);


CREATE TABLE Tags (
  tagname VARCHAR(100)   NOT NULL ,
  Topic_id INTEGER   NOT NULL   ,
PRIMARY KEY(tagname, Topic_id),
  FOREIGN KEY(Topic_id)
    REFERENCES Topic(id));


CREATE OR REPLACE TRIGGER AINC_Tags
BEFORE INSERT  ON Tags
FOR EACH ROW 
BEGIN 
  IF (:NEW.tagname IS NULL) THEN 
    SELECT GlobalSequence.NEXTVAL INTO :NEW.tagname FROM DUAL; 
  END IF; 
END; 

/


CREATE INDEX IFK_Rel_32 ON Tags (Topic_id);


CREATE TABLE Userdetails (
  UsersId INTEGER   NOT NULL ,
  first_name VARCHAR(255)   NOT NULL ,
  last_name VARCHAR(255)    ,
  gender VARCHAR(20)   NOT NULL ,
  country VARCHAR(40)   NOT NULL ,
  DOB DATE      ,
PRIMARY KEY(UsersId)  ,
  FOREIGN KEY(UsersId)
    REFERENCES Users(UsersId));


CREATE INDEX Userdetails_FKIndex2 ON Userdetails (UsersId);


CREATE INDEX IFK_has ON Userdetails (UsersId);


CREATE TABLE Work (
  UsersId INTEGER   NOT NULL ,
  Companyname VARCHAR(255)   NOT NULL ,
  wfrom INTEGER   NOT NULL ,
  wto INTEGER   NOT NULL   ,
PRIMARY KEY(UsersId)  ,
  FOREIGN KEY(UsersId)
    REFERENCES Users(UsersId));


CREATE INDEX Work_FKIndex1 ON Work (UsersId);


CREATE INDEX IFK_has ON Work (UsersId);


CREATE TABLE Education (
  UsersId INTEGER   NOT NULL ,
  InstituteName VARCHAR(255)   NOT NULL ,
  efrom INTEGER    ,
  eto INTEGER      ,
PRIMARY KEY(UsersId,InstituteName)  ,
  FOREIGN KEY(UsersId)
    REFERENCES Users(UsersId));


CREATE INDEX Education_FKIndex1 ON Education (UsersId);


CREATE INDEX IFK_has ON Education (UsersId);


CREATE TABLE Activity (
  id INTEGER   NOT NULL ,
  UsersId INTEGER   NOT NULL ,
  times TIMESTAMP   NOT NULL ,
  detail VARCHAR(255)   NOT NULL   ,
PRIMARY KEY(id)  ,
  FOREIGN KEY(UsersId)
    REFERENCES Users(UsersId));


CREATE INDEX Activity_FKIndex1 ON Activity (UsersId);


CREATE OR REPLACE TRIGGER AINC_Activity
BEFORE INSERT  ON Activity
FOR EACH ROW 
BEGIN 
  IF (:NEW.id IS NULL) THEN 
    SELECT GlobalSequence.NEXTVAL INTO :NEW.id FROM DUAL; 
  END IF; 
END; 

/


CREATE INDEX IFK_performs ON Activity (UsersId);


CREATE TABLE Usersskill (
  UsersId INTEGER   NOT NULL ,
  Skillname VARCHAR(200)   NOT NULL   ,
PRIMARY KEY(UsersId, Skillname)  ,
  FOREIGN KEY(UsersId)
    REFERENCES Users(UsersId));


CREATE INDEX Users_has_skills_FKIndex1 ON Usersskill (UsersId);


CREATE INDEX IFK_has ON Usersskill (UsersId);


CREATE TABLE Notification (
  id INTEGER   NOT NULL ,
  UsersId INTEGER   NOT NULL ,
  detail VARCHAR(255)   NOT NULL ,
  status INTEGER   NOT NULL ,
  time TIMESTAMP   NOT NULL   ,
PRIMARY KEY(id)  ,
  FOREIGN KEY(UsersId)
    REFERENCES Users(UsersId));


CREATE INDEX Table_27_FKIndex1 ON Notification (UsersId);


CREATE INDEX IFK_gets ON Notification (UsersId);


CREATE TABLE Message (
  id INTEGER   NOT NULL ,
  senderid INTEGER   NOT NULL ,
  messagetext CLOB    ,
  time TIMESTAMP      ,
PRIMARY KEY(id)  ,
  FOREIGN KEY(senderid)
    REFERENCES Users(UsersId));


CREATE INDEX Message_FKIndex1 ON Message (senderid);


CREATE OR REPLACE TRIGGER AINC_Message
BEFORE INSERT  ON Message
FOR EACH ROW 
BEGIN 
  IF (:NEW.id IS NULL) THEN 
    SELECT GlobalSequence.NEXTVAL INTO :NEW.id FROM DUAL; 
  END IF; 
END; 

/


CREATE INDEX IFK_sends ON Message (senderid);


CREATE TABLE UsersLinks (
  Links_id INTEGER   NOT NULL ,
  UsersId INTEGER   NOT NULL ,
  link VARCHAR(100)      ,
PRIMARY KEY(Links_id, UsersId)    ,
  FOREIGN KEY(UsersId)
    REFERENCES Users(UsersId),
  FOREIGN KEY(Links_id)
    REFERENCES Links(id));


CREATE INDEX Users_has_Links_FKIndex1 ON UsersLinks (UsersId);
CREATE INDEX Users_has_Links_FKIndex2 ON UsersLinks (Links_id);


CREATE INDEX IFK_has ON UsersLinks (UsersId);
CREATE INDEX IFK_isof ON UsersLinks (Links_id);


CREATE TABLE TopicUsers (
  Topic_id INTEGER   NOT NULL ,
  UsersId INTEGER   NOT NULL ,
  time TIMESTAMP   NOT NULL   ,
PRIMARY KEY(Topic_id, UsersId)    ,
  FOREIGN KEY(UsersId)
    REFERENCES Users(UsersId),
  FOREIGN KEY(Topic_id)
    REFERENCES Topic(id));


CREATE INDEX Users_has_Topic_FKIndex1 ON TopicUsers (UsersId);
CREATE INDEX Users_has_Topic_FKIndex2 ON TopicUsers (Topic_id);


CREATE INDEX IFK_isin ON TopicUsers (UsersId);
CREATE INDEX IFK_has ON TopicUsers (Topic_id);


CREATE TABLE TopicMessage (
  Topic_id INTEGER   NOT NULL ,
  Message_id INTEGER   NOT NULL   ,
PRIMARY KEY(Topic_id, Message_id)    ,
  FOREIGN KEY(Topic_id)
    REFERENCES Topic(id),
  FOREIGN KEY(Message_id)
    REFERENCES Message(id));


CREATE INDEX Topic_has_Message_FKIndex1 ON TopicMessage (Topic_id);
CREATE INDEX TopicMessage_FKIndex2 ON TopicMessage (Message_id);


CREATE INDEX IFK_has ON TopicMessage (Topic_id);
CREATE INDEX IFK_isof ON TopicMessage (Message_id);


CREATE TABLE Friendship (
  id INTEGER   NOT NULL ,
  Senderid INTEGER   NOT NULL ,
  Recieverid INTEGER   NOT NULL ,
  time TIMESTAMP   NOT NULL ,
  status_2 integer  DEFAULT 0 NOT NULL   ,
PRIMARY KEY(id)    ,
  FOREIGN KEY(Senderid)
    REFERENCES Users(UsersId),
  FOREIGN KEY(Recieverid)
    REFERENCES Users(UsersId));


CREATE INDEX Friendship_FKIndex1 ON Friendship (Senderid);
CREATE INDEX Friendship_FKIndex2 ON Friendship (Recieverid);


CREATE OR REPLACE TRIGGER AINC_Friendship
BEFORE INSERT  ON Friendship
FOR EACH ROW 
BEGIN 
  IF (:NEW.id IS NULL) THEN 
    SELECT GlobalSequence.NEXTVAL INTO :NEW.id FROM DUAL; 
  END IF; 
END; 

/


CREATE INDEX IFK_sends ON Friendship (Senderid);
CREATE INDEX IFK_recieves ON Friendship (Recieverid);


CREATE TABLE permission (
  Topic_id INTEGER   NOT NULL ,
  UsersId INTEGER   NOT NULL ,
  status_2 integer  DEFAULT 0 NOT NULL   ,
PRIMARY KEY(Topic_id, UsersId)    ,
  FOREIGN KEY(Topic_id)
    REFERENCES Topic(id),
  FOREIGN KEY(UsersId)
    REFERENCES Users(UsersId));


CREATE INDEX permission_FKIndex1 ON permission (Topic_id);
CREATE INDEX permission_FKIndex2 ON permission (UsersId);


CREATE INDEX IFK_has ON permission (Topic_id);
CREATE INDEX IFK_request ON permission (UsersId);


CREATE TABLE Groupactivity (
  id INTEGER   NOT NULL ,
  UsersId INTEGER   NOT NULL ,
  Topicid INTEGER   NOT NULL ,
  times TIMESTAMP   NOT NULL ,
  detail VARCHAR(255)   NOT NULL   ,
PRIMARY KEY(id)    ,
  FOREIGN KEY(Topicid)
    REFERENCES Topic(id),
  FOREIGN KEY(UsersId)
    REFERENCES Users(UsersId));


CREATE INDEX Groupactivity_FKIndex1 ON Groupactivity (Topicid);
CREATE INDEX Groupactivity_FKIndex2 ON Groupactivity (UsersId);


CREATE OR REPLACE TRIGGER AINC_Groupactivity
BEFORE INSERT  ON Groupactivity
FOR EACH ROW 
BEGIN 
  IF (:NEW.id IS NULL) THEN 
    SELECT GlobalSequence.NEXTVAL INTO :NEW.id FROM DUAL; 
  END IF; 
END; 

/


CREATE INDEX IFK_has ON Groupactivity (Topicid);
CREATE INDEX IFK_performs ON Groupactivity (UsersId);


CREATE TABLE Thread (
  id INTEGER   NOT NULL ,
  user2id INTEGER   NOT NULL ,
  User1id INTEGER   NOT NULL ,
  lastmessage VARCHAR(255) ,
  lastupdate TIMESTAMP   NOT NULL   ,
PRIMARY KEY(id)    ,
  FOREIGN KEY(user2id)
    REFERENCES Users(UsersId),
  FOREIGN KEY(User1id)
    REFERENCES Users(UsersId));


CREATE INDEX Thread_FKIndex2 ON Thread (user2id);
CREATE INDEX Thread_FKIndex2 ON Thread (User1id);


CREATE OR REPLACE TRIGGER AINC_Thread
BEFORE INSERT  ON Thread
FOR EACH ROW 
BEGIN 
  IF (:NEW.id IS NULL) THEN 
    SELECT GlobalSequence.NEXTVAL INTO :NEW.id FROM DUAL; 
  END IF; 
END; 

/


CREATE INDEX IFK_partof ON Thread (user2id);
CREATE INDEX IFK_partof ON Thread (User1id);


CREATE TABLE phonenumber (
  phonenumber VARCHAR(20)   NOT NULL ,
  UsersId INTEGER   NOT NULL ,
  type_id INTEGER   NOT NULL   ,
PRIMARY KEY(phonenumber, UsersId)  ,
  FOREIGN KEY(UsersId)
    REFERENCES Users(UsersId),
  FOREIGN KEY(type_id)
    REFERENCES numbertype(typeid));


CREATE INDEX number_FKIndex1 ON phonenumber (UsersId);


CREATE OR REPLACE TRIGGER AINC_phonenumber
BEFORE INSERT  ON phonenumber
FOR EACH ROW 
BEGIN 
  IF (:NEW.phonenumber IS NULL) THEN 
    SELECT GlobalSequence.NEXTVAL INTO :NEW.phonenumber FROM DUAL; 
  END IF; 
END; 

/


CREATE INDEX IFK_has ON phonenumber (UsersId);
CREATE INDEX IFK_isof ON phonenumber (type_id);


CREATE TABLE UsersFriend (
  UsersId INTEGER   NOT NULL ,
  FriendId INTEGER   NOT NULL ,
  Friendship_id INTEGER   NOT NULL   ,
PRIMARY KEY(UsersId, FriendId)      ,
  FOREIGN KEY(UsersId)
    REFERENCES Users(UsersId),
  FOREIGN KEY(FriendId)
    REFERENCES Users(UsersId),
  FOREIGN KEY(Friendship_id)
    REFERENCES Friendship(id));


CREATE INDEX Users_has_Users_FKIndex1 ON UsersFriend (UsersId);
CREATE INDEX Users_has_Users_FKIndex2 ON UsersFriend (FriendId);
CREATE INDEX UsersFriend_FKIndex3 ON UsersFriend (Friendship_id);


CREATE INDEX IFK_has ON UsersFriend (UsersId);
CREATE INDEX IFK_has ON UsersFriend (FriendId);
CREATE INDEX IFK_becomes ON UsersFriend (Friendship_id);


CREATE TABLE Messagenot (
  notid INTEGER   NOT NULL ,
  UsersId INTEGER   NOT NULL ,
  Topic_id INTEGER    ,
  Thread_id INTEGER    ,
PRIMARY KEY(notid)      ,
  FOREIGN KEY(Thread_id)
    REFERENCES Thread(id),
  FOREIGN KEY(Topic_id)
    REFERENCES Topic(id),
  FOREIGN KEY(UsersId)
    REFERENCES Users(UsersId));


CREATE INDEX Messagenot_FKIndex1 ON Messagenot (Thread_id);
CREATE INDEX Messagenot_FKIndex2 ON Messagenot (Topic_id);
CREATE INDEX Messagenot_FKIndex3 ON Messagenot (UsersId);


CREATE OR REPLACE TRIGGER AINC_Messagenot
BEFORE INSERT  ON Messagenot
FOR EACH ROW 
BEGIN 
  IF (:NEW.notid IS NULL) THEN 
    SELECT GlobalSequence.NEXTVAL INTO :NEW.notid FROM DUAL; 
  END IF; 
END; 

/


CREATE INDEX IFK_Rel_37 ON Messagenot (Thread_id);
CREATE INDEX IFK_Rel_38 ON Messagenot (Topic_id);
CREATE INDEX IFK_Rel_39 ON Messagenot (UsersId);


CREATE TABLE ThreadMessage (
  Thread_id INTEGER   NOT NULL ,
  Message_id INTEGER   NOT NULL   ,
PRIMARY KEY(Thread_id, Message_id)    ,
  FOREIGN KEY(Thread_id)
    REFERENCES Thread(id),
  FOREIGN KEY(Message_id)
    REFERENCES Message(id));


CREATE INDEX Thread_has_Message_FKIndex1 ON ThreadMessage (Thread_id);
CREATE INDEX ThreadMessage_FKIndex2 ON ThreadMessage (Message_id);
CREATE INDEX IFK_has ON ThreadMessage (Thread_id);
CREATE INDEX IFK_isof ON ThreadMessage (Message_id);

create or replace trigger topicadd 
after insert on topicusers
for each row
begin
not1(:new.topic_id,:new.usersid);
end;
/

create or replace procedure not1 (topic_id integer,uid integer)
is
adname users.username%type;
uname users.username%type;
auserid topic.adminid%type;
tname topic.name%type;
detail notification.detail%type :='';
begin
select u.username , t.adminid into adname,auserid from users u, topic t where t.adminid=u.usersid and t.id=topic_id;
select name into tname from topic where id=topic_id;
select username into uname from users where usersid=uid;
detail :=adname || ' add you in Topic ' || tname;
insert into notification values(not_seq.nextval,uid,detail,0,sysdate);
end;
/


create or replace trigger sendrequest
after insert on friendship
for each row
begin
not3(:new.senderid,:new.recieverid);
end;
/

create or replace procedure not3(id integer,rid integer)
is
uname users.username%type;
detail notification.detail%type:='';
begin 
select username into uname from users where usersid = id;
detail:= uname|| ' Send you a Friend Request ';
insert into notification values(not_seq.nextval,rid,detail,0,sysdate);
end;
/ 

create or replace trigger acceptrequest
after update of status_2 on friendship
for each row
begin
not4(:new.senderid,:new.recieverid);
end;
/

create or replace procedure not4(id integer,rid integer)
is
uname users.username%type;
detail notification.detail%type:='';
begin 
select username into uname from users where usersid = rid;
detail:= uname || ' Accepted Friend Request ';
insert into notification values(not_seq.nextval,id,detail,0,sysdate);
end;
/ 

create or replace trigger joingroup
after insert on permission
for each row
begin
not5(:new.topic_id,:new.usersid);
end;
/

create or replace procedure not5(tid integer,uid integer)
is
uname users.username%type;
tname topic.name%type;
aid users.usersid%type;
detail notification.detail%type:='';
begin 
dbms_output.put_line(tid|| ' ' ||uid);
select username into uname from users where usersid = uid;
select adminid,name into aid,tname from topic where id =tid;
detail:= uname || ' requested to join the Topic ' || tname;
insert into notification values(not_seq.nextval,aid,detail,0,sysdate);
end;
/
          
insert into numbertype values('1','Home');
insert into numbertype values('2','Work');
insert into numbertype values('3','Mobile');

insert into links values('1','Facebook');
insert into links values('2','Twitter');
insert into links values('3','Instagram');
insert into links values('4','Google+');