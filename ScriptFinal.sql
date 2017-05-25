DROP TABLE ThreadMessage;
DROP TABLE Messagenot;
DROP TABLE UsersFriend;
DROP TABLE Thread;
DROP TABLE topiccategory;
DROP TABLE phonenumber;
DROP TABLE Groupactivity;
DROP TABLE permission;
DROP TABLE Friendship;
DROP TABLE Userdetails;
DROP TABLE UsersLinks;
DROP TABLE TopicUsers;
DROP TABLE TopicMessage;
DROP TABLE Message;
DROP TABLE Notification;
DROP TABLE Work;
DROP TABLE Usersskill;
DROP TABLE Education;
DROP TABLE Activity;
DROP TABLE Tags;
DROP TABLE Topic;
DROP TABLE Usersinterest;
DROP TABLE category;
DROP TABLE Users;
DROP TABLE Links;
DROP TABLE numbertype;
DROP SEQUENCE user_seq;
DROP SEQUENCE act_seq;
DROP SEQUENCE friend_seq;
DROP SEQUENCE gr_act;
DROP SEQUENCE msg_seq;
DROP SEQUENCE msgnot_seq;
DROP SEQUENCE thread_seq;
DROP SEQUENCE topic_seq;

CREATE TABLE numbertype (
  typeid INTEGER   NOT NULL ,
  typename VARCHAR(20)   NOT NULL  ,
PRIMARY KEY(typeid));

insert into numbertype values('1','Home');
insert into numbertype values('2','Work');
insert into numbertype values('3','Mobile');

CREATE TABLE Links (
  id INTEGER   NOT NULL ,
  name VARCHAR(255)      ,
PRIMARY KEY(id));

insert into links values('1','Facebook');
insert into links values('2','Twitter');
insert into links values('3','Instagram');
insert into links values('4','Google+');

CREATE TABLE Users (
  UsersId INTEGER   NOT NULL ,
  Username VARCHAR(40) UNIQUE  NOT NULL ,
  pass VARCHAR(40)   NOT NULL ,
  emailid VARCHAR(255)   NOT NULL ,
  timeofregistration TIMESTAMP   NOT NULL ,
  status_2 INTEGER  DEFAULT 0 NOT NULL ,
  reference VARCHAR(40)      ,
  line INTEGER DEFAULT 0 ,
    userpic VARCHAR(100),
    usercover VARCHAR(100),
PRIMARY KEY(UsersId));

create sequence user_seq start with 100 increment by 1;

CREATE TABLE Usersinterest (
  UsersId INTEGER   NOT NULL ,
  Intrest VARCHAR(200)   NOT NULL   ,
PRIMARY KEY(UsersId, Intrest),
  FOREIGN KEY(UsersId)
    REFERENCES Users(UsersId));


CREATE TABLE Topic (
  id INTEGER   NOT NULL ,
  adminid INTEGER   NOT NULL ,
  name VARCHAR(255)   NOT NULL ,
  description VARCHAR(255)    ,
  time TIMESTAMP   NOT NULL   ,
PRIMARY KEY(id),
  FOREIGN KEY(adminid)
    REFERENCES Users(UsersId));

create sequence topic_seq start with 200 increment by 1;

CREATE TABLE Tags (
  tagname VARCHAR(100)   NOT NULL ,
  id INTEGER   NOT NULL   ,
PRIMARY KEY(tagname, id),
  FOREIGN KEY(id)
    REFERENCES Topic(id));

CREATE TABLE Activity (
  id INTEGER   NOT NULL ,
  UsersId INTEGER   NOT NULL ,
  times TIMESTAMP   NOT NULL ,
  detail VARCHAR(255)   NOT NULL   ,
PRIMARY KEY(id),
  FOREIGN KEY(UsersId)
    REFERENCES Users(UsersId));

create sequence act_seq start with 300 increment by 1;

CREATE TABLE Education (
  UsersId INTEGER   NOT NULL ,
  InstituteName VARCHAR(255)   NOT NULL ,
  efrom INTEGER    ,
  eto INTEGER      ,
PRIMARY KEY(UsersId,InstituteName,efrom),
  FOREIGN KEY(UsersId)
    REFERENCES Users(UsersId));



CREATE TABLE Usersskill (
  UsersId INTEGER   NOT NULL ,
  Skillname VARCHAR(200)   NOT NULL   ,
PRIMARY KEY(UsersId, Skillname),
  FOREIGN KEY(UsersId)
    REFERENCES Users(UsersId));



CREATE TABLE Work (
  UsersId INTEGER   NOT NULL ,
  Companyname VARCHAR(255)   NOT NULL ,
  wfrom INTEGER,
 wto INTEGER    ,
    PRIMARY KEY(UsersId,Companyname,wfrom),
  FOREIGN KEY(UsersId)
    REFERENCES Users(UsersId));



CREATE TABLE Notification (
  id INTEGER   NOT NULL ,
  UsersId INTEGER   NOT NULL ,
  detail VARCHAR(255)      ,
  status INTEGER,
    time timestamp,
PRIMARY KEY(id),
  FOREIGN KEY(UsersId)
    REFERENCES Users(UsersId));

create sequence not_seq start with 500 increment by 1;

CREATE TABLE Message (
  id INTEGER   NOT NULL ,
  senderid INTEGER   NOT NULL ,
  messagetext TEXT    ,
  time TIMESTAMP      ,
PRIMARY KEY(id),
  FOREIGN KEY(senderid)
    REFERENCES Users(UsersId));

create sequence msg_seq start with 600 increment by 1;

CREATE TABLE TopicMessage (
  Topic_id INTEGER   NOT NULL ,
  Message_id INTEGER   NOT NULL ,
PRIMARY KEY(Topic_id, Message_id),
  FOREIGN KEY(Topic_id)
    REFERENCES Topic(id),
  FOREIGN KEY(Message_id)
    REFERENCES Message(id));



CREATE TABLE TopicUsers (
  Topic_id INTEGER   NOT NULL ,
  UsersId INTEGER   NOT NULL ,
  time TIMESTAMP   NOT NULL   ,
PRIMARY KEY(Topic_id, UsersId),
  FOREIGN KEY(UsersId)
    REFERENCES Users(UsersId),
  FOREIGN KEY(Topic_id)
    REFERENCES Topic(id));



CREATE TABLE UsersLinks (
  Links_id INTEGER   NOT NULL ,
  UsersId INTEGER   NOT NULL ,
  link VARCHAR(100)      ,
PRIMARY KEY(Links_id, UsersId),
  FOREIGN KEY(UsersId)
    REFERENCES Users(UsersId),
  FOREIGN KEY(Links_id)
    REFERENCES Links(id));



CREATE TABLE Userdetails (
  UsersId INTEGER   NOT NULL ,
  first_name VARCHAR(255)   NOT NULL ,
  last_name VARCHAR(255)    ,
  gender VARCHAR(10) check (gender in ('Male','Female'))    ,
  country VARCHAR(40),
  DOB DATE    ,
PRIMARY KEY(UsersId),
  FOREIGN KEY(UsersId)
    REFERENCES Users(UsersId)
);



CREATE TABLE Friendship (
  id INTEGER   NOT NULL ,
  Senderid INTEGER   NOT NULL ,
  Recieverid INTEGER   NOT NULL ,
  times TIMESTAMP   NOT NULL ,
  status_2 INTEGER  DEFAULT 0 NOT NULL   ,
PRIMARY KEY(id),
  FOREIGN KEY(Senderid)
    REFERENCES Users(UsersId),
  FOREIGN KEY(Recieverid)
    REFERENCES Users(UsersId));

create sequence friend_seq start with 700 increment by 1;

CREATE TABLE permission (
  Topic_id INTEGER   NOT NULL ,
  UsersId INTEGER   NOT NULL ,
  status_2 INTEGER  DEFAULT 0 NOT NULL   ,
PRIMARY KEY(Topic_id, UsersId),
  FOREIGN KEY(Topic_id)
    REFERENCES Topic(id),
  FOREIGN KEY(UsersId)
    REFERENCES Users(UsersId));



CREATE TABLE Groupactivity (
  id INTEGER   NOT NULL ,
  UsersId INTEGER   NOT NULL ,
  Topicid INTEGER   NOT NULL ,
  times TIMESTAMP   NOT NULL ,
  detail VARCHAR(255)   NOT NULL   ,
PRIMARY KEY(id),
  FOREIGN KEY(Topicid)
    REFERENCES Topic(id),
  FOREIGN KEY(UsersId)
    REFERENCES Users(UsersId));

create sequence gr_act start with 800 increment by 1;


CREATE TABLE phonenumber (
  phonenumber VARCHAR(20)   NOT NULL ,
  UsersId INTEGER   NOT NULL ,
  type_id INTEGER   NOT NULL   ,
PRIMARY KEY(phonenumber, UsersId),
  FOREIGN KEY(UsersId)
    REFERENCES Users(UsersId),
  FOREIGN KEY(type_id)
    REFERENCES numbertype(typeid));



CREATE TABLE topiccategory (
  category VARCHAR(255)   NOT NULL ,
  Topic_id INTEGER   NOT NULL   ,
PRIMARY KEY(category, Topic_id),
   FOREIGN KEY(Topic_id)
    REFERENCES Topic(id));

CREATE TABLE Thread (
  id INTEGER   NOT NULL ,
  user2id INTEGER   NOT NULL ,
  User1id INTEGER   NOT NULL ,
  lastmessage VARCHAR(255)    ,
  lastupdate TIMESTAMP      ,
PRIMARY KEY(id),
  FOREIGN KEY(user2id)
    REFERENCES Users(UsersId),
  FOREIGN KEY(User1id)
    REFERENCES Users(UsersId));

create sequence thread_seq start with 900 increment by 1;

CREATE TABLE UsersFriend (
  UsersId INTEGER   NOT NULL ,
  FriendId INTEGER   NOT NULL ,
  Friendship_id INTEGER   NOT NULL   ,
PRIMARY KEY(UsersId, FriendId),
  FOREIGN KEY(UsersId)
    REFERENCES Users(UsersId),
  FOREIGN KEY(FriendId)
    REFERENCES Users(UsersId),
  FOREIGN KEY(Friendship_id)
    REFERENCES Friendship(id));

CREATE TABLE Messagenot (
  notid INTEGER   NOT NULL ,
  UsersId INTEGER   NOT NULL ,
  Topic_id INTEGER    ,
  Thread_id INTEGER      ,
PRIMARY KEY(notid),
  FOREIGN KEY(UsersId)
    REFERENCES Users(UsersId),
  FOREIGN KEY(Thread_id)
    REFERENCES Thread(id),
  FOREIGN KEY(Topic_id)
    REFERENCES Topic(id));

create sequence msgnot_seq start with 1000 increment by 1;


CREATE TABLE ThreadMessage (
  Thread_id INTEGER   NOT NULL ,
  Message_id INTEGER   NOT NULL   ,
PRIMARY KEY(Thread_id, Message_id),
  FOREIGN KEY(Thread_id)
    REFERENCES Thread(id),
  FOREIGN KEY(Message_id)
    REFERENCES Message(id));
    
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

create or replace trigger topicdelete
before delete on topic
for each row
begin
not2(:old.id);
end;
/

create or replace procedure not2 (topic_id integer)
is
begin
delete from topicusers where topic_id=topic_id;
delete from permission where topic_id=topic_id;
delete from tags where id =topic_id;
delete from topicmessage where topic_id =topic_id;
delete from topiccategory where topic_id =topic_id;
delete from groupactivity where topicid =topic_id;
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
