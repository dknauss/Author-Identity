# Metadata Models for Publishers, Publications, and Contributors

## Overview

There are several overlapping ecosystems of metadata standards for
describing publishers, publications, works, and contributor roles. They
come from three main traditions:

1.  Semantic Web / Linked Data ontologies (RDF/OWL)
2.  Library and bibliographic cataloging standards
3.  Publishing industry exchange standards

Below is a structured overview of the major models and how they relate.

------------------------------------------------------------------------

# Core Semantic Web vocabularies used for publishing metadata

## Schema.org

Widely used structured data vocabulary for the web.

Key classes:

-   Person
-   Organization
-   CreativeWork
-   Book
-   Article
-   Periodical
-   PublicationIssue
-   PublicationVolume

Key relationships:

-   author
-   editor
-   publisher
-   contributor
-   isPartOf
-   hasPart

Example relationships:

Article → Issue → Volume → Periodical\
Person → author/editor → CreativeWork

Purpose:

-   Web discovery
-   Lightweight linked data publishing

------------------------------------------------------------------------

## Dublin Core

One of the oldest cross-domain metadata models.

Common properties:

-   dc:title
-   dc:creator
-   dc:contributor
-   dc:publisher
-   dc:date
-   dc:type
-   dc:identifier

------------------------------------------------------------------------

## FOAF

Ontology for describing people and organizations.

Key classes:

-   foaf:Person
-   foaf:Organization

Often used for author identity.

------------------------------------------------------------------------

## SKOS

Used for controlled vocabularies and subject classifications.

------------------------------------------------------------------------

# Semantic Web bibliographic ontologies

## BIBO

Bibliographic Ontology for describing documents and citations.

Examples:

-   bibo:Book
-   bibo:Article
-   bibo:Journal
-   bibo:Issue

------------------------------------------------------------------------

## SPAR Ontologies

Semantic Publishing and Referencing Ontologies.

Major modules:

-   FaBiO --- bibliographic entities
-   CiTO --- citation typing
-   BiRO --- bibliographic records
-   DoCO --- document components
-   PRO --- contributor roles
-   PWO --- publishing workflow
-   PSO --- publication status

These enable modeling the full lifecycle of publications.

------------------------------------------------------------------------

# Library metadata models

## FRBR / IFLA LRM

Conceptual model for bibliographic entities.

Core entities:

Work\
Expression\
Manifestation\
Item

Example:

Work → Hamlet\
Expression → English text\
Manifestation → Penguin edition\
Item → physical copy

------------------------------------------------------------------------

# Publishing industry metadata standards

## ONIX for Books

Industry XML format for exchanging book metadata.

Used by:

-   publishers
-   distributors
-   retailers
-   libraries

Metadata includes:

-   contributor roles
-   ISBN
-   publisher
-   subjects
-   marketing data

------------------------------------------------------------------------

# Scholarly identity systems

These are identifier infrastructures.

## ORCID

Persistent identifier for researchers.

## ISNI

Identifier for creators and organizations.

## ROR

Identifier for research institutions.

## DOI / Crossref

Identifiers and metadata for scholarly works.

------------------------------------------------------------------------

# Typical publishing entity graph

    Person
      ├─ author → Work
      ├─ editor → Work
      └─ memberOf → Organization

    Organization
      ├─ publisherOf → Manifestation
      └─ employs → Person

    Work
      ├─ realizedAs → Expression
      └─ hasPart → Chapter

    Expression
      └─ embodiedIn → Manifestation

    Manifestation
      ├─ publishedBy → Publisher
      ├─ identifiedBy → ISBN
      └─ partOf → Series
