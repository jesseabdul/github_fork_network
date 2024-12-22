######################################################################
# Generating degree domain
# 
# For use as x axis when plotting degree_distribution on y axis: 
#  > plot(degree_domain(g), degree_distribution(g), ...) 
# The first value of degree_distribution is always for degree 0, but
# if we call plot only on the degree distribution plot will assume 
# that the first value is index 1. Adding degree_domain gives the 
# correct x axis values. 
# 
# Dan Suthers, Feburary 1, 2018 
######################################################################

degree_domain <- function(g, mode="all") {
  return(1:max(degree(g, mode=mode)))
}

######################################################################
# Pau 