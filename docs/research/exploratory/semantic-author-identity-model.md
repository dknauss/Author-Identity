# Semantic Author Identity Model

## Vision

A modern publishing ecosystem should treat author identity as a
first-class, portable entity that exists independently from any single
platform.

This model connects:

-   authors
-   organizations
-   publications
-   identifiers
-   contributor roles

into a unified knowledge graph.

------------------------------------------------------------------------

# Core identity layer

Persistent identifiers:

ORCID --- researcher identity\
ISNI --- creator identity\
ROR --- institutional identity\
DOI --- publication identity

These identifiers allow entities to be linked across platforms.

------------------------------------------------------------------------

# Core entities

Author (Person) Organization Publication Role Identifier

------------------------------------------------------------------------

# Graph model

    Person (ORCID)
       │
       ├── memberOf → Organization (ROR)
       │
       ├── holdsRole → AuthorRole
       │                 │
       │                 └── contributesTo → Publication (DOI)
       │
       └── identifiedBy → ORCID

------------------------------------------------------------------------

# Contributor roles

Roles should be explicit objects rather than simple labels.

Examples:

author\
editor\
translator\
illustrator\
reviewer

------------------------------------------------------------------------

# Platform mapping

Example mapping for WordPress:

User → Person\
Post → Work\
Post meta → Identifier\
Taxonomy → Concept

------------------------------------------------------------------------

# Author identity portability

The goal is portable identity across systems:

WordPress\
Crossref\
ORCID\
Wikidata\
OpenAlex

This enables:

-   contributor attribution
-   scholarly reputation tracking
-   machine-readable authorship
-   decentralized identity for creators
