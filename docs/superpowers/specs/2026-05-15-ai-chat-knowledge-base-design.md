# AI Chat Knowledge Base Design

## Goal

Add a public AI chat assistant to the Dream Digital website that answers visitors using only local, admin-approved knowledge. The assistant must not browse the web, invent facts, or use information outside the knowledge base and selected CMS context.

## Scope

The first version includes:

- A public chat widget for website visitors.
- A local AI knowledge base managed in the admin CMS.
- Required imports for Markdown, CSV, and PDF files.
- Manual CMS entries for curated Q&A, commercial notes, support procedures, and country-specific information.
- Conversation history and lead capture.
- Strict fallback behavior when the knowledge base does not contain an answer.

Out of scope for the first version:

- Voice chat.
- External website crawling.
- Automatic publication of imported content without review.
- Complex PDF table reconstruction.
- Vector search as a hard dependency.

## Core Rule

The assistant answers only from provided sources.

System behavior:

- Use only published knowledge chunks returned by the local retrieval step.
- Use the visitor locale, current page, and country context only as filters or routing hints.
- If the answer is not present in the supplied chunks, say that Dream Digital cannot confirm it from the available information and offer WhatsApp or the contact form.
- Never invent prices, contractual terms, countries covered, delivery times, phone numbers, or technical capabilities.
- Never claim to have searched the web.

## Admin Module

Add an admin section named `Assistant IA` with:

- `Conversations`: list visitor chats, lead status, and transcript.
- `Base de connaissances`: list and edit knowledge entries and chunks.
- `Importer un fichier`: upload Markdown, CSV, and PDF documents.
- `Parametres`: enable chat, configure greeting, model, source limits, and display rules.

Access is controlled through the existing role profile system with new permissions:

- `ai_chat.view`
- `ai_chat.manage`
- `ai_knowledge.view`
- `ai_knowledge.manage`

## Knowledge Sources

The knowledge base has two source types.

### Manual CMS Entries

Fields:

- Title.
- Category.
- Country: global, cd, ci, cg.
- Locale: fr, en.
- Content.
- Status: draft, published, archived.
- Priority.
- Optional expiration date.

Published entries are chunked and made available to the assistant.

### File Imports

Supported file types:

- Markdown: extract text directly.
- CSV: map columns such as `question`, `answer`, `category`, `country`, and `locale`.
- PDF: extract readable text server-side.

Import workflow:

- Store the original file locally.
- Extract text into reviewable chunks.
- Keep imported chunks as draft by default.
- Let admins edit, merge, delete, and publish chunks.
- Record import errors without blocking the rest of the CMS.

## Data Model

Proposed tables:

- `ai_knowledge_sources`: original files or manual sources, metadata, status, locale, country, uploader.
- `ai_knowledge_chunks`: searchable blocks used by retrieval, linked to a source.
- `ai_chat_sessions`: visitor session, page, locale, country, lead state.
- `ai_chat_messages`: user and assistant messages, selected source references, token/cost metadata when available.
- `ai_chat_leads`: captured name, email, phone, WhatsApp, company, country, need, consent.
- `ai_chat_settings`: activation, model, greeting, prompt, retrieval limits, display rules.

## Retrieval

The MVP uses PostgreSQL full-text search:

- Search only published, non-expired chunks.
- Filter by locale first.
- Prefer current country, then global.
- Prefer higher priority chunks.
- Return a limited number of relevant chunks to the AI model.

Vector search can be added later if the knowledge base grows large or full-text relevance is not enough.

## Public Chat Flow

Flow:

1. Visitor opens chat widget.
2. Widget creates or resumes an `ai_chat_session`.
3. Visitor sends a message.
4. Laravel validates length, rate limits the IP/session, and retrieves local chunks.
5. Laravel sends the model a strict system prompt plus retrieved chunks.
6. Assistant response is stored and returned to the widget.
7. If the user asks for human help or shares contact details, the flow creates or updates an `ai_chat_lead`.

The API key remains server-side only. No public JavaScript receives provider credentials.

## UI Behavior

The widget should be small, professional, and visible on public pages when enabled.

Expected features:

- Greeting in the current locale.
- Text chat.
- Clear fallback to WhatsApp or contact form.
- Lead capture fields only when needed.
- Loading and error states.
- Mobile-friendly layout.

The admin screens follow the existing Dream Digital admin visual patterns.

## Error Handling

Expected failure behavior:

- No relevant source found: answer with fallback, not speculation.
- AI provider unavailable: apologize briefly and offer contact form or WhatsApp.
- Import partially fails: save successful chunks and show failed rows/pages.
- Invalid file type: reject with a clear admin message.
- Oversized file: reject according to configured limits.

## Security And Privacy

Requirements:

- Rate limit public chat endpoints.
- Validate and sanitize imported content.
- Store files under private storage unless explicitly exposed.
- Require consent before storing lead contact details.
- Never expose API keys or raw prompts publicly.
- Avoid logging secrets or sensitive personal data unnecessarily.
- Keep role-based permissions for all admin AI screens.

## Testing

Feature tests:

- Chat refuses to answer without published knowledge.
- Chat uses only chunks matching locale/country/status.
- Markdown import creates draft chunks.
- CSV import maps rows into draft chunks.
- PDF import handles readable text and reports extraction failures.
- Admin permissions protect settings and imports.
- Rate limits apply to public chat endpoint.

Manual QA:

- Public widget on desktop and mobile.
- FR/EN answers.
- Country-specific fallback.
- Admin import and publish workflow.
- Lead capture and transcript review.

## Launch Strategy

Phase 1:

- Build settings, knowledge tables, import pipeline, retrieval, backend chat endpoint, and simple widget.

Phase 2:

- Add richer admin review tools, lead qualification, and source references in transcripts.

Phase 3:

- Improve retrieval with embeddings/vector search if needed.

