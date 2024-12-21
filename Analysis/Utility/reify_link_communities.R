######################################################################
# reify_link_communities 
# 
# Adds vertices representing link communities to a graph, with the
# intention that they be used for visualization in Gephi. 
# REQUIRES that V(g)$name be defined (copy from $label if needed). 
# 
# Dan Suthers, originally Nov 16, 2016
# April 7, 2018 Comment added concerning computation of node.ids
# Nov 14 2019 DS: Minor reordering for clarity; no functional change 
# Apr 11 2021 DS: Replaced use of factor with 1:lc$numbers[[3]] to 
#   generate community IDs, as nodeclusters$cluster is no longer 
#   returning a factor, and this is faster given that cluster IDs 
#   are sequential integers (see $clusters documentation).
# Apr  8 2022 DS: Using add_edges to add all edges at once so graph 
#   is copied only twice total. Also updated 4/11/21 rationale. 
# Nov  8 2023 DS: In other code, discovered that linkcomm works better if
#   $name is defined, especially if with human-interpretable $labels. This
#   motivated making the availability of $name a requirement, which
#   simplifies some of the mappings (no more "which" or "V(g)[...]$label"
#   needed). This also solves problems we had with reify_link_communities
#   needing to be revised depending on whether $id is numeric or character.
#
######################################################################
library(igraph)
library(linkcomm)

# Community labels will be constructed from IDs to indicate 
# that they are communities. 
#
comm_label <- function (id) {return(paste0("COMM_", id))}

# Given a graph g and a legal link community object lc for
# that graph, returns a copy of the graph with communities
# added as vertices. We don't compute the link community
# within this function as we want the user to retain full
# control of that computation through its various parameters. 
#
reify_link_communities <- function(g, lc) {
	
  #  Mark existing vertices as not being community nodes. 
  
  V(g)$comm_p <- FALSE 

  # Names of community vertices for each cluster.
  
  comm_names <- as.character(lapply(1:lc$numbers[[3]],  
                                    comm_label))
  
  # Create a community vertex for each cluster, using the above
  # labels. Add these vertices all at once for one graph copy. 
  
  g <- add_vertices(g, 
                    length(comm_names), 
                    label = comm_names,
                    name  = comm_names, # added in 2023 
                    comm_p = TRUE)
  
  # The source for each directed edge will be vertices that are in link
  # communities (some may not be, so we use $node, not V(g)).
  
  source_names <- lc$nodeclusters$node # 2023: no longer as.numeric 
  
  # The target for each directed edge will be the corresponding vertices
  # added above, identified by mapping cluster to labels we made. 
  
  target_names <- as.vector(vapply(lc$nodeclusters$cluster, 
                                   comm_label, 
                                   character(1)))

  # Add edges from original nodes to community vertices all at once.
  # add_edges wants a list of alternating source and target vertices,
  # that is, pairs of node + community vertices. We use an anonymous
  # function to make pairs, and then flatten the list. 
  
  g <- add_edges(g, 
                 unlist(lapply(1:length(source_names), 
                               function(i) { 
                                 c(source_names[i], 
                                   target_names[i]) # removed which in 2023 
                                 }
                               )
                        )
                 )
  
  return(g)
}

######################################################################
# Pau 