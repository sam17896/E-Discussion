CREATE TABLE Users (
  UsersId INTEGER   NOT NULL ,
  Username VARCHAR(40) UNIQUE  NOT NULL ,
  pass VARCHAR(40)   NOT NULL ,
  emailid VARCHAR(255)   NOT NULL ,
  timeofregistration TIMESTAMP   NOT NULL ,
  status_2 INTEGER DEFAULT 0 check(status_2 in (0,1)) NOT NULL,
    reference VARCHAR(40),
PRIMARY KEY(UsersId));

Create Sequence user_seq start with 100 increment by 1;

CREATE TABLE Userdetails (
  UsersId INTEGER   NOT NULL ,
  first_name VARCHAR(255)   NOT NULL ,
  last_name VARCHAR(255)    ,
  city VARCHAR(20)    ,
  country VARCHAR(40)    ,
  gender VARCHAR(20) check(gender in ('male','female')) NOT NULL   ,
  DOB DATE    ,
PRIMARY KEY(UsersId),
  FOREIGN KEY(UsersId)
    REFERENCES Users(UsersId));

create table tags(
        name VARCHAR(200) NOT NULL,
        id INTEGER NOT NULL,
        PRIMARY KEY (name,id), 
        FOREIGN KEY (id) REFERENCES topic(id));
        
CREATE TABLE Userskill(
    name VARCHAR(200) NOT NULL,
    userid INTEGER NOT NULL,
    PRIMARY KEY(name,userid),
    FOREIGN KEY (userid) REFERENCES Users(UsersId)
);        

CREATE TABLE Userinterest(
    name VARCHAR(200) NOT NULL,
    userid INTEGER NOT NULL,
    PRIMARY KEY(name,userid),
    FOREIGN KEY (userid) REFERENCES Users(UsersId)
);        

CREATE TABLE Topiccat(
    name VARCHAR(200) NOT NULL,
    id INTEGER NOT NULL,
    PRIMARY KEY(name,id),
    FOREIGN KEY (id) REFERENCES Topic(Id)
);        

 create or replace view new as (select skillname from usersskill where usersid=1 union select interest from usersinterest where usersid=1);

 select * from topic t where t.id in (select tc.topic_id from topiccategory tc where tc.category =any(select * from new)) or t.id in (select td.id from tags td where td.name=any(select * from new));

        
CREATE TABLE Activity (
  id INTEGER NOT NULL,
  UsersId INTEGER NOT NULL  ,
  times TIMESTAMP  NOT NULL  ,
  detail VARCHAR(255)  NOT NULL ,
PRIMARY KEY(id) ,
    FOREIGN KEY(UsersId) 
    REFERENCES Users(UsersId));
    
CREATE TABLE GroupActivity (
  id INTEGER NOT NULL,
  UsersId INTEGER NOT NULL  ,
    topicid INTEGER NOT NULL,
  times TIMESTAMP  NOT NULL  ,
  detail VARCHAR(255)  NOT NULL ,
PRIMARY KEY(id) ,
    FOREIGN KEY(UsersId) 
    REFERENCES Users(UsersId),
    FOREIGN KEY(topicId) 
    REFERENCES topic(Id));

CREATE TABLE TopicUsers(
    Topic_id INTEGER NOT NULL,
    usersid INTEGER NOT NULL,
    times TIMESTAMP NOT NULL,
    PRIMARY KEY(Topic_id,usersid),
    FOREIGN KEY (usersid) REFERENCES Users(UsersId),
    FOREIGN KEY (Topic_id) REFERENCES topic(id)
);
CREATE TABLE Message (
  id INTEGER   NOT NULL ,
  senderid INTEGER   NOT NULL ,
  messagetext VARCHAR(255)    ,
  time TIMESTAMP      ,
PRIMARY KEY(id),
  FOREIGN KEY(senderid)
    REFERENCES Users(UsersId));

CREATE TABLE TopicMessage (
  Topic_id INTEGER   NOT NULL ,
  Message_id INTEGER   NOT NULL   ,
PRIMARY KEY(Topic_id, Message_id),
  FOREIGN KEY(Topic_id)
    REFERENCES Topic(id),
  FOREIGN KEY(Message_id)
    REFERENCES Message(id));
    
    CREATE TABLE Friendship (
  id INTEGER   NOT NULL ,
  Senderid INTEGER   NOT NULL ,
  Recieverid INTEGER   NOT NULL ,
  time TIMESTAMP   NOT NULL ,
   status_2 INTEGER DEFAULT 0 check(status_2 in (0,1)) NOT NULL    ,
PRIMARY KEY(id),
  FOREIGN KEY(Senderid)
    REFERENCES Users(UsersId),
  FOREIGN KEY(Recieverid)
    REFERENCES Users(UsersId));
    
    create sequence friend_seq start with 500 increment by 1;
    
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
    
    CREATE TABLE Thread (
  id INTEGER   NOT NULL ,
  user2id INTEGER   NOT NULL ,
  User1id INTEGER   NOT NULL   ,
PRIMARY KEY(id),
  FOREIGN KEY(user2id)
    REFERENCES Users(UsersId),
  FOREIGN KEY(User1id)
    REFERENCES Users(UsersId));
    
    CREATE TABLE ThreadMessage (
  Thread_id INTEGER   NOT NULL ,
  Message_id INTEGER   NOT NULL   ,
PRIMARY KEY(Thread_id, Message_id),
  FOREIGN KEY(Thread_id)
    REFERENCES Thread(id),
  FOREIGN KEY(Message_id)
    REFERENCES Message(id));