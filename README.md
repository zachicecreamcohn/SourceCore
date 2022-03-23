# SourceCore | Technical Theatre News
[Visit the site (*SourceCore*)][http://ec2-3-142-143-25.us-east-2.compute.amazonaws.com/news_site]


## Creative Portion
I may have gone a bit overboard on this project. I wanted to build something that I could be proud of. There are many features I'd still like to add sometime in the future, but I'm still quite happy with how things turned out.

### Like/Dislikes
On top of the assigned comment functions, I added the ability to like and dislike comments. If you've already liked/disliked a certain comment, you aren't able to do so again. This data is stored in the `emotes` table and is deleted when the corresponding comment is deleted. The tally works like Reddit; it can go into the negative range.

### Authorship
While the assignment was to allow for posting links with titles and a message body, I added the ability to post more conventional articles. Using the [Parsedown](https://github.com/erusev/parsedown) library and a theme (adjusted to my taste) from my favorite Markdown editor (WriteMonkey), users with Author permissions can write articles in Markdown format. 

#### Requesting Author Status
Any user can request author status via the link on the 'Add Story' page or the button on the user profile. The admin, via the 'Author Requests' page can view and accept requests for authorship.

### Some Smaller Additions

#### Images
For every post (Link or Markdown-based), the user can input an image url. This serves as the cover image for that post. This cover image is present on the explore pages and on the individual pages.

#### User Profile
I have also added a user profile page that allows the logged in user to change their username, display name, first name, last name, and email. The user can also view their admin status, author status on this page. If a user is not an Author, they can request this permission here.

#### Post Categories
There are four major headings on my site (+ "Explore" which simply lets you view all posts). When a post (be it a link or Markdown post) is published, the poster can choose to identify which of the four main categories they'd like to associate their post with. They can choose all four or none or anything in between. These categories help organize the content on the main page. When a user clicks the "Gear" header, for example, they will see all posts to which the subject "Gear" has been assigned. 


## A few words about aesthetics
Obviously, how a website looks is not the focus of our course, but I wanted to put some effort into that, too. The design of my site is based a bit on digg.com, but with many changes.


*I hope you enjoy!*
