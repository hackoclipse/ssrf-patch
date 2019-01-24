# ssrf-patch
As a challange i tryed to make a patch for every variant of internal server side request forgery.
it's protected again's:

every internal ip in url. ( as well octal/hexdeminal/binery encoded )
location header redirect to internal ip's.
internal ip returned using dns.
2 ip's on one domain using dns.
