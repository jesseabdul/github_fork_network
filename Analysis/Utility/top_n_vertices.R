######################################################################
# top_n_vertices (topnv)
# Returns the top N vertices ("top n v") in a graph under a set of
# values, which can be anything. Typical use: ranking nodes by
# centrality metrics, e.g., topnv(g, degree(g))$label
# Mar  8 2022 Dan Suthers created from old code. 
# Sep 29 2024 Dan Suthers added top_n_vertices for more explicit code
######################################################################

require('igraph')

top_n_vertices <- function(graph, values, n=10) {
  return(V(graph)[order(values, decreasing=TRUE)[1:n]])
}

# top_n_vertices is recommended for writing readable code, but 
# topnv is handy for interactive use 

topnv <- top_n_vertices

######################################################################


