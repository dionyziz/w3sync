*****w3sync***** is a simple tool for web application deployment. It is intended
to be used by medium and big-sized projects to manage deployment of code from
develompent to testing and from testing to production.

What deploying is
=================
If you've worked on a somewhat bigger web application project, you'll know that
editing code live is a no-no. This means that code should be written and tested
on a different environment than the users see. In his book "Building Scalable
Websites", Cal Henderson, the lead engineer behind the team that built Flickr
writes about editing code live on your web application:

> Fairly obvious large problems occur with this approach as time goes by. Any
> mistakes you make are immediately shown to your users. Features in development
> are immediately released, whether complete or not, as soon as you start
> linking them into the live pages of your site. As soon as you have any kind of
> serious user base, these sorts of issues become unacceptable. Early on you'll
> find you need to split your environments into two parts or, more often, three
> or more.

That's the reason we run different copies of our web application for different
needs. These copies are called *environments*. Often, developers run two
different environments of their web application:
One *development* environment and one *production* environment, sometimes also
called the *live* environment. The development environment is used by developers
to test the changes they make and see if their new code commits work as
expected. It is also used to share links with their colleagues, help them test
their code cooperatively, and ask them about bugs. The production environment is
what the users see and interact with.

These two environments are, ideally, completely isolated from each other. This
means that the production environment doesn't know that the development
environment even exists, and vice versa. If something is screwed up in the
development environment, it doesn't at all affect the production environment.
The isolation requirement implies that the two environments do not share a
common database, file server, memcache server, or what-have-you.

In bigger projects, sometimes three or more environments are used. A common
third is to have a *testing* environment where the QA or hallway testing team
has access to, where beta features are pushed.

*Deploying* is the process of pushing code from one environment to the next in
the pipeline: From development to production if you have two environments; from
development to testing and from testing to production if you have three.

w3sync
======
w3sync is a meta-tool written in PHP which assists you with the deployment
process. It will help you do the following:

 * A one-step build
 * Logs of your deployment history
 * Access control of who can deploy
 * One-click rollbacks
 * Deployment messages
 * Deployment history UI including diffs

Because deployment for every project is different, it is impossible to write one
tool to do the deployment for you. For example, a particular application may be
minifying its Javascript and CSS and splitting it to two different servers
during deployment; or another project may be using a custom CDN where deployment
of static content is needed. Different projects also use different version
control systems and different programming languages which require particular
treatment.

This tool is only a basis that you can use to build a good deployment system
for your web application. You still need to write the code that does the actual
deployment.

Caution
=======
This tool was developed for the deployment of the *Zino* social network. I have
now decided to edit its code to make it more generalized and to open source it.
The current version has many parts that are specific to Zino and that you need
to modify to make it work for you. In addition, the current version is designed
to work with subversion as the version control system of the managed web
application. This tool is intended for experienced web developers. Read the code
and make sure you understand what's going on before using this tool.

TODO
====

 * Split out a settings.php file which customizes the behavior of the tool.
 * Specify a particular object-oriented interface to be implemented by the user
   in order to specify the code that does the actual deployment.
 * Allow the user to choose between version control systems; in particular, add
   support for git and mercurial.

License
=======
w3sync is licensed under the **MIT License**:

Copyright (C) 2012 Dionysis "dionyziz" Zindros <dionyziz@gmail.com>

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
the Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
