FROM nlnetlabs/routinator:latest

VOLUME /root/.rpki-cache


ADD https://www.arin.net/resources/rpki/arin-rfc7730.tal /root/.rpki-cache/tals/arin.tal

WORKDIR /

EXPOSE 3323/tcp
CMD ["routinator", "rtrd", "-a","-l","0.0.0.0:3323"]
